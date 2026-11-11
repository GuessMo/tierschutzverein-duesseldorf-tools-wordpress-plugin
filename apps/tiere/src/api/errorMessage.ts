interface WordPressError {
  code?: string;
  message?: string;
  data?: { params?: Record<string, string> };
}

const SESSION_CODES = new Set(["rest_cookie_invalid_nonce", "rest_not_logged_in"]);

export function errorMessage(body: unknown, status: number): string {
  const error = (body ?? {}) as WordPressError;
  if (error.code && SESSION_CODES.has(error.code)) {
    return "Deine Sitzung ist abgelaufen. Lade die Seite neu und melde dich erneut an.";
  }
  const params = error.data?.params;
  if (params) return Object.values(params).join(" ");
  return error.message ?? `Die Anfrage ist fehlgeschlagen (Status ${status}).`;
}
