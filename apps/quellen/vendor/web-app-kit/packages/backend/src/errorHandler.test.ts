import assert from "node:assert/strict";
import { Writable } from "node:stream";
import { test } from "node:test";
import Fastify from "fastify";
import { registerErrorHandler } from "./errorHandler.js";
import {
  AppError,
  ConfigurationError,
  NotFoundError,
  TooManyRequestsError,
  ValidationError,
} from "./errors.js";

function createLogSink() {
  const lines: string[] = [];
  const stream = new Writable({
    write(chunk, _encoding, callback) {
      lines.push(String(chunk));
      callback();
    },
  });
  return { lines, stream };
}

function buildApp(thrown: () => Error) {
  const sink = createLogSink();
  const app = Fastify({ logger: { level: "error", stream: sink.stream } });
  registerErrorHandler(app);
  app.get("/fail", async () => {
    throw thrown();
  });
  app.post(
    "/items",
    { schema: { body: { type: "object", required: ["name"] } } },
    async () => ({ ok: true }),
  );
  return { app, logs: sink.lines };
}

function withStatus(message: string, statusCode: number) {
  return Object.assign(new Error(message), { statusCode });
}

test("AppError subclasses answer with their status and message", async () => {
  const { app, logs } = buildApp(() => new NotFoundError("Project", "7"));
  const response = await app.inject({ url: "/fail" });
  assert.equal(response.statusCode, 404);
  assert.match(String(response.headers["content-type"]), /application\/json/);
  assert.deepEqual(response.json(), { error: "Project not found: 7" });
  assert.equal(logs.length, 0);
});

test("ValidationError maps to 422 with its message", async () => {
  const { app } = buildApp(() => new ValidationError("name is required"));
  const response = await app.inject({ url: "/fail" });
  assert.equal(response.statusCode, 422);
  assert.deepEqual(response.json(), { error: "name is required" });
});

test("a plain AppError keeps its message with status 500", async () => {
  const { app } = buildApp(() => new AppError("known failure"));
  const response = await app.inject({ url: "/fail" });
  assert.equal(response.statusCode, 500);
  assert.deepEqual(response.json(), { error: "known failure" });
});

test("foreign errors below 500 pass status and message through", async () => {
  const { app, logs } = buildApp(() => withStatus("Forbidden by upstream", 403));
  const response = await app.inject({ url: "/fail" });
  assert.equal(response.statusCode, 403);
  assert.deepEqual(response.json(), { error: "Forbidden by upstream" });
  assert.equal(logs.length, 0);
});

test("Fastify schema validation answers 400 with the validation message", async () => {
  const { app } = buildApp(() => new Error("unused"));
  const response = await app.inject({ method: "POST", url: "/items", payload: {} });
  assert.equal(response.statusCode, 400);
  assert.match(response.json().error, /name/);
});

for (const [label, thrown] of [
  ["plain errors", () => new Error("database password wrong")],
  ["errors with status 500 or higher", () => withStatus("gateway details", 503)],
  ["ConfigurationError", () => new ConfigurationError("staticDir missing")],
] as const) {
  test(`${label} answer a generic 500 and are logged`, async () => {
    const { app, logs } = buildApp(thrown);
    const response = await app.inject({ url: "/fail" });
    assert.equal(response.statusCode, 500);
    assert.deepEqual(response.json(), { error: "Internal server error" });
    assert.equal(logs.length, 1);
    assert.match(logs[0], new RegExp(thrown().message));
  });
}

test("TooManyRequestsError answers 429 with a Retry-After header", async () => {
  const { app } = buildApp(
    () => new TooManyRequestsError("Too many attempts", 89.2),
  );
  const response = await app.inject({ url: "/fail" });
  assert.equal(response.statusCode, 429);
  assert.equal(response.headers["retry-after"], "90");
  assert.deepEqual(response.json(), { error: "Too many attempts" });
});

test("TooManyRequestsError without a delay sends no Retry-After", async () => {
  const { app } = buildApp(() => new TooManyRequestsError("Too many attempts"));
  const response = await app.inject({ url: "/fail" });
  assert.equal(response.statusCode, 429);
  assert.equal(response.headers["retry-after"], undefined);
});
