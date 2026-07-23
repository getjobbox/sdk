import { HttpClient, buildUserAgent } from './http/client';
import { JobsResource } from './resources/jobs';
import type { JobBoxClientOptions } from './types/common';

const DEFAULT_BASE_URL = 'https://api.getjobbox.com';

export class JobBox {
  readonly jobs: JobsResource;
  private readonly http: HttpClient;

  constructor(options: JobBoxClientOptions) {
    const apiKey = String(options.apiKey || '').trim();
    if (!apiKey) {
      throw new Error('JobBox SDK requires an apiKey (set JOBBOX_API_KEY or pass apiKey).');
    }

    const baseUrl = (options.baseUrl || DEFAULT_BASE_URL).replace(/\/$/, '');
    const timeoutMs = options.timeoutMs ?? 30_000;
    const maxRetries = options.maxRetries ?? 2;
    const fetchImpl = options.fetch ?? globalThis.fetch.bind(globalThis);

    if (typeof fetchImpl !== 'function') {
      throw new Error('JobBox SDK requires a fetch implementation (Node.js >= 18).');
    }

    this.http = new HttpClient({
      apiKey,
      baseUrl,
      timeoutMs,
      maxRetries,
      userAgent: buildUserAgent(options.appName),
      fetchImpl,
      defaultHeaders: options.defaultHeaders || {},
    });

    this.jobs = new JobsResource(this.http);
  }
}
