import { JobBoxApiError, JobBoxNetworkError } from '../errors';
import type { ApiSuccessEnvelope } from '../types/common';
import { VERSION } from '../version';

export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

export interface HttpClientConfig {
  apiKey: string;
  baseUrl: string;
  timeoutMs: number;
  maxRetries: number;
  userAgent: string;
  fetchImpl: typeof fetch;
  defaultHeaders: Record<string, string>;
}

function sleep(ms: number): Promise<void> {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

function parseRetryAfterMs(header: string | null): number | null {
  if (!header) return null;
  const asInt = Number.parseInt(header, 10);
  if (Number.isFinite(asInt) && asInt >= 0) {
    return asInt * 1000;
  }
  const asDate = Date.parse(header);
  if (Number.isFinite(asDate)) {
    return Math.max(0, asDate - Date.now());
  }
  return null;
}

function backoffMs(attempt: number, retryAfterMs: number | null): number {
  if (retryAfterMs != null) return retryAfterMs;
  const base = Math.min(8000, 250 * 2 ** attempt);
  const jitter = Math.floor(Math.random() * 100);
  return base + jitter;
}

function shouldRetry(method: HttpMethod, status: number): boolean {
  if (method !== 'GET') return false;
  return status === 429 || status >= 500;
}

export function toCsv(value: string | string[] | undefined): string | undefined {
  if (value == null) return undefined;
  if (Array.isArray(value)) {
    const parts = value.map((v) => String(v).trim()).filter(Boolean);
    return parts.length ? parts.join(',') : undefined;
  }
  const trimmed = String(value).trim();
  return trimmed || undefined;
}

export function buildQuery(params: Record<string, string | number | boolean | undefined | null>): string {
  const sp = new URLSearchParams();
  for (const [key, value] of Object.entries(params)) {
    if (value === undefined || value === null || value === '') continue;
    sp.set(key, String(value));
  }
  const qs = sp.toString();
  return qs ? `?${qs}` : '';
}

export class HttpClient {
  private readonly config: HttpClientConfig;

  constructor(config: HttpClientConfig) {
    this.config = config;
  }

  async request<T>(
    method: HttpMethod,
    path: string,
    options: { query?: Record<string, string | number | boolean | undefined | null>; body?: unknown } = {}
  ): Promise<T> {
    const { query, body } = options;
    const url = `${this.config.baseUrl.replace(/\/$/, '')}/api/v1${path}${buildQuery(query || {})}`;

    let lastError: unknown;

    for (let attempt = 0; attempt <= this.config.maxRetries; attempt += 1) {
      const controller = new AbortController();
      const timer = setTimeout(() => controller.abort(), this.config.timeoutMs);

      try {
        const headers: Record<string, string> = {
          Accept: 'application/json',
          'X-JobBox-Api-Key': this.config.apiKey,
          'User-Agent': this.config.userAgent,
          ...this.config.defaultHeaders,
        };
        if (body !== undefined) {
          headers['Content-Type'] = 'application/json';
        }

        const init: RequestInit = {
          method,
          headers,
          signal: controller.signal,
        };
        if (body !== undefined) {
          init.body = JSON.stringify(body);
        }

        const response = await this.config.fetchImpl(url, init);

        const requestId =
          response.headers.get('x-request-id') ||
          response.headers.get('x-jobbox-request-id') ||
          null;

        let parsed: unknown = null;
        const text = await response.text();
        if (text) {
          try {
            parsed = JSON.parse(text) as unknown;
          } catch {
            parsed = text;
          }
        }

        if (!response.ok) {
          if (attempt < this.config.maxRetries && shouldRetry(method, response.status)) {
            const wait = backoffMs(attempt, parseRetryAfterMs(response.headers.get('retry-after')));
            await sleep(wait);
            continue;
          }

          const errBody = parsed && typeof parsed === 'object' ? (parsed as Record<string, unknown>) : null;
          const message =
            (errBody && typeof errBody.message === 'string' && errBody.message) ||
            `JobBox API request failed with status ${response.status}`;
          const code = errBody && typeof errBody.code === 'string' ? errBody.code : null;
          throw new JobBoxApiError(message, {
            status: response.status,
            code,
            requestId,
            body: parsed,
          });
        }

        if (parsed && typeof parsed === 'object' && 'data' in (parsed as object)) {
          return (parsed as ApiSuccessEnvelope<T>).data;
        }
        return parsed as T;
      } catch (error) {
        lastError = error;
        if (error instanceof JobBoxApiError) {
          throw error;
        }
        const isAbort =
          error instanceof Error &&
          (error.name === 'AbortError' || /aborted/i.test(error.message));
        if (attempt < this.config.maxRetries && method === 'GET' && isAbort) {
          await sleep(backoffMs(attempt, null));
          continue;
        }
        if (isAbort) {
          throw new JobBoxNetworkError('JobBox API request timed out', error);
        }
        if (attempt < this.config.maxRetries && method === 'GET') {
          await sleep(backoffMs(attempt, null));
          continue;
        }
        throw new JobBoxNetworkError(
          error instanceof Error ? error.message : 'JobBox API network error',
          error
        );
      } finally {
        clearTimeout(timer);
      }
    }

    throw new JobBoxNetworkError('JobBox API request failed after retries', lastError);
  }
}

export function buildUserAgent(appName?: string): string {
  const base = `JobBoxNodeSDK/${VERSION}`;
  if (appName && String(appName).trim()) {
    return `${base} ${String(appName).trim()}`;
  }
  return base;
}
