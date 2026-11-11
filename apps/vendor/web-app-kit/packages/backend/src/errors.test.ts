import assert from "node:assert/strict";
import { test } from "node:test";
import {
  AppError,
  BadRequestError,
  ConfigurationError,
  ConflictError,
  NotFoundError,
  TooManyRequestsError,
  UnauthorizedError,
  UpstreamError,
  ValidationError,
} from "./errors.js";

const MESSAGE_ERRORS = [
  { ErrorClass: BadRequestError, name: "BadRequestError", statusCode: 400 },
  { ErrorClass: UnauthorizedError, name: "UnauthorizedError", statusCode: 401 },
  { ErrorClass: ConflictError, name: "ConflictError", statusCode: 409 },
  { ErrorClass: ValidationError, name: "ValidationError", statusCode: 422 },
  { ErrorClass: UpstreamError, name: "UpstreamError", statusCode: 502 },
] as const;

test("AppError defaults to status 500", () => {
  const error = new AppError("boom");
  assert.ok(error instanceof Error);
  assert.equal(error.statusCode, 500);
  assert.equal(error.message, "boom");
});

test("NotFoundError names resource and id with status 404", () => {
  const error = new NotFoundError("Project", "42");
  assert.ok(error instanceof AppError);
  assert.equal(error.statusCode, 404);
  assert.equal(error.name, "NotFoundError");
  assert.equal(error.message, "Project not found: 42");
});

for (const { ErrorClass, name, statusCode } of MESSAGE_ERRORS) {
  test(`${name} keeps its message and maps to status ${statusCode}`, () => {
    const error = new ErrorClass("details");
    assert.ok(error instanceof AppError);
    assert.equal(error.statusCode, statusCode);
    assert.equal(error.name, name);
    assert.equal(error.message, "details");
  });
}

test("ConfigurationError is no AppError and carries no status code", () => {
  const error = new ConfigurationError("missing staticDir");
  assert.ok(error instanceof Error);
  assert.equal(error instanceof AppError, false);
  assert.equal(error.name, "ConfigurationError");
  assert.equal(error.message, "missing staticDir");
  assert.equal("statusCode" in error, false);
});

test("TooManyRequestsError maps to 429 and keeps the retry delay", () => {
  const error = new TooManyRequestsError("slow down", 90);
  assert.ok(error instanceof AppError);
  assert.equal(error.statusCode, 429);
  assert.equal(error.name, "TooManyRequestsError");
  assert.equal(error.message, "slow down");
  assert.equal(error.retryAfterSeconds, 90);
  const withoutDelay = new TooManyRequestsError("slow down");
  assert.equal(withoutDelay.retryAfterSeconds, undefined);
});
