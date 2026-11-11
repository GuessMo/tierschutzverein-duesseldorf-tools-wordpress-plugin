import { ApiError } from "@web-app-kit/frontend";
import { readBootstrap } from "@/config/bootstrap";
import { errorMessage } from "./errorMessage";

type Method = "GET" | "POST" | "PATCH" | "PUT" | "DELETE";

async function request<T>(path: string, init: RequestInit): Promise<T> {
  const { restUrl, nonce } = readBootstrap();
  const response = await fetch(`${restUrl}${path}`, {
    ...init,
    credentials: "same-origin",
    headers: { ...init.headers, "X-WP-Nonce": nonce },
  });
  const data: unknown = await response.json().catch(() => null);
  if (!response.ok) throw new ApiError(response.status, errorMessage(data, response.status));
  return data as T;
}

export function send<T>(method: Method, path: string, body?: unknown): Promise<T> {
  return request<T>(path, {
    method,
    headers: { "Content-Type": "application/json" },
    body: body === undefined ? undefined : JSON.stringify(body),
  });
}

export function upload<T>(path: string, form: FormData): Promise<T> {
  return request<T>(path, { method: "POST", body: form });
}
