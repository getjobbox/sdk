export class JobBoxApiError extends Error {
  readonly status: number;
  readonly code: string | null;
  readonly requestId: string | null;
  readonly body: unknown;

  constructor(
    message: string,
    options: {
      status: number;
      code?: string | null;
      requestId?: string | null;
      body?: unknown;
    }
  ) {
    super(message);
    this.name = 'JobBoxApiError';
    this.status = options.status;
    this.code = options.code ?? null;
    this.requestId = options.requestId ?? null;
    this.body = options.body ?? null;
  }
}

export class JobBoxNetworkError extends Error {
  readonly causeError: unknown;

  constructor(message: string, cause?: unknown) {
    super(message);
    this.name = 'JobBoxNetworkError';
    this.causeError = cause;
  }
}
