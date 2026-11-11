import assert from "node:assert/strict";
import { test } from "node:test";
import Fastify from "fastify";
import { readAppVersion, registerVersionRoute } from "./version.js";

test("readAppVersion returns the trimmed APP_VERSION", () => {
  assert.equal(readAppVersion({ APP_VERSION: " v0.7.1-3-gabc1234 " }), "v0.7.1-3-gabc1234");
});

test("readAppVersion is undefined when APP_VERSION is missing or blank", () => {
  assert.equal(readAppVersion({}), undefined);
  assert.equal(readAppVersion({ APP_VERSION: "  " }), undefined);
});

test("version route answers with the version and no-store", async () => {
  const app = Fastify();
  registerVersionRoute(app, { version: "v0.7.1" });
  const response = await app.inject({ method: "GET", url: "/api/version" });
  assert.equal(response.statusCode, 200);
  assert.equal(response.headers["cache-control"], "no-store");
  assert.deepEqual(response.json(), { version: "v0.7.1" });
});

test("version route answers null without a version and honours a custom path", async () => {
  const app = Fastify();
  registerVersionRoute(app, { version: undefined, path: "/version" });
  const response = await app.inject({ method: "GET", url: "/version" });
  assert.deepEqual(response.json(), { version: null });
});
