import assert from "node:assert/strict";
import { test } from "node:test";
import { ApiError, UserFacingError, describeError } from "./apiClient.js";

const FALLBACK = "Fallback";

test("describeError shows the message of an ApiError", () => {
  assert.equal(describeError(new ApiError(404, "Not found"), FALLBACK), "Not found");
});

test("describeError shows the message of a UserFacingError", () => {
  const error = new UserFacingError("Invalid JSON");
  assert.equal(describeError(error, FALLBACK), "Invalid JSON");
});

test("describeError hides a browser network error behind the fallback", () => {
  assert.equal(describeError(new TypeError("Failed to fetch"), FALLBACK), FALLBACK);
});

test("describeError hides a JSON parse error behind the fallback", () => {
  assert.equal(describeError(new SyntaxError("Unexpected token <"), FALLBACK), FALLBACK);
});

test("describeError returns the fallback for non-errors", () => {
  assert.equal(describeError("boom", FALLBACK), FALLBACK);
});
