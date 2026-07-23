export interface ApiSuccessEnvelope<T> {
  success: boolean;
  code?: string;
  message?: string;
  data: T;
}

export interface JobBoxClientOptions {
  /** Partner API key (plaintext). Prefer env JOBBOX_API_KEY. */
  apiKey: string;
  /** Default: https://api.getjobbox.com */
  baseUrl?: string;
  /** Request timeout in ms. Default: 30000 */
  timeoutMs?: number;
  /** Max retries for idempotent GETs on 429/5xx. Default: 2 */
  maxRetries?: number;
  /** Optional app name appended to User-Agent */
  appName?: string;
  /** Injectable fetch for tests / edge runtimes */
  fetch?: typeof fetch;
  /** Extra default headers */
  defaultHeaders?: Record<string, string>;
}
