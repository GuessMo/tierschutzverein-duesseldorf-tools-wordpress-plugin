export class ApiError extends Error {
  constructor(
    public status: number,
    message: string,
  ) {
    super(message);
    this.name = "ApiError";
  }
}

export class UserFacingError extends Error {
  constructor(message: string) {
    super(message);
    this.name = "UserFacingError";
  }
}

export interface ApiClient {
  backendUrl: string;
  request<T>(path: string, init?: RequestInit): Promise<T>;
  assetUrl(path: string): string;
}

/**
 * One client per app, bound to its own backend URL. Talks the same
 * request/response shape web-app-kit's backend errorHandler produces:
 * JSON body with an "error" string on failure, matched status code.
 */
export function createApiClient(backendUrl: string): ApiClient {
  return {
    backendUrl,
    async request<T>(path: string, init?: RequestInit): Promise<T> {
      const isFormData = init?.body instanceof FormData;
      const headers: HeadersInit = init?.body && !isFormData ? { "Content-Type": "application/json" } : {};
      const response = await fetch(`${backendUrl}${path}`, { ...init, headers });

      if (response.status === 204) return undefined as T;

      const data = await response.json();
      if (!response.ok) throw new ApiError(response.status, data.error ?? "Request failed");
      return data as T;
    },
    assetUrl(path: string): string {
      return `${backendUrl}${path}`;
    },
  };
}

export function describeError(error: unknown, fallback = "Something went wrong"): string {
  if (error instanceof ApiError || error instanceof UserFacingError) return error.message;
  return fallback;
}
