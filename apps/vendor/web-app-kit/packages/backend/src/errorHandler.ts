import type { FastifyError, FastifyInstance } from "fastify";
import { AppError, TooManyRequestsError } from "./errors.js";

/**
 * AppError instances and any error with statusCode < 500 return their real
 * message to the client. Anything else logs server-side and returns a generic
 * 500 — never leaks stack traces/internals, but also never masks a legitimate
 * upstream error (e.g. a third-party API 4xx) behind "Internal server error".
 */
export function registerErrorHandler(app: FastifyInstance): void {
  app.setErrorHandler<FastifyError>((error, _request, reply) => {
    const retryAfter =
      error instanceof TooManyRequestsError
        ? error.retryAfterSeconds
        : undefined;
    if (retryAfter !== undefined) {
      reply.header("retry-after", String(Math.ceil(retryAfter)));
    }
    if (error instanceof AppError) {
      return reply.code(error.statusCode).send({ error: error.message });
    }
    if (error.statusCode && error.statusCode < 500) {
      return reply.code(error.statusCode).send({ error: error.message });
    }
    app.log.error(error);
    return reply.code(500).send({ error: "Internal server error" });
  });
}
