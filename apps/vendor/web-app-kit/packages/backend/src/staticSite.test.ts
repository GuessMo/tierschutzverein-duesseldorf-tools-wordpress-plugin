import assert from "node:assert/strict";
import { test } from "node:test";
import { mkdirSync, mkdtempSync, writeFileSync } from "node:fs";
import { tmpdir } from "node:os";
import path from "node:path";
import Fastify from "fastify";
import { registerErrorHandler } from "./errorHandler.js";
import { ConfigurationError } from "./errors.js";
import { isServerPath, registerStaticSite } from "./staticSite.js";

const HTML = { accept: "text/html" };
const INDEX = "<html>app</html>";

function createStaticDir(parent = mkdtempSync(path.join(tmpdir(), "web-app-kit-static-"))) {
  const staticDir = path.join(parent, "dist");
  mkdirSync(path.join(staticDir, "assets"), { recursive: true });
  writeFileSync(path.join(staticDir, "index.html"), INDEX);
  writeFileSync(path.join(staticDir, "assets", "app-1a2b.js"), "console.log(1)");
  writeFileSync(path.join(staticDir, ".env"), "SECRET=1");
  return staticDir;
}

async function buildApp(serverPaths?: string[], staticDir = createStaticDir()) {
  const app = Fastify();
  registerErrorHandler(app);
  app.get("/s.js", async () => "snippet");
  await registerStaticSite(app, { staticDir, serverPaths });
  return app;
}

test("deep app paths fall back to index.html with no-cache", async () => {
  const app = await buildApp();
  const response = await app.inject({ url: "/experiments/42", headers: HTML });
  assert.equal(response.statusCode, 200);
  assert.equal(response.body, INDEX);
  assert.equal(response.headers["cache-control"], "no-cache");
});

test("HEAD requests for app pages get index.html headers", async () => {
  const app = await buildApp();
  const response = await app.inject({ method: "HEAD", url: "/boards/1", headers: HTML });
  assert.equal(response.statusCode, 200);
  assert.match(response.headers["content-type"] ?? "", /text\/html/);
});

test("default server path /api never falls back, incl. query and trailing slash", async () => {
  const app = await buildApp();
  for (const url of ["/api", "/api/", "/api/missing", "/api?x=1", "/api/missing?x=1"]) {
    const response = await app.inject({ url, headers: HTML });
    assert.equal(response.statusCode, 404, url);
    assert.match(response.headers["content-type"] ?? "", /application\/json/, url);
  }
});

test("custom server paths replace the default", async () => {
  const app = await buildApp(["/api", "/mcp", "/health"]);
  for (const url of ["/mcp/missing", "/health/x", "/health?probe=1"]) {
    const response = await app.inject({ url, headers: HTML });
    assert.equal(response.statusCode, 404, url);
  }
  const lookalike = await app.inject({ url: "/apis", headers: HTML });
  assert.equal(lookalike.body, INDEX);
});

test("registered routes are served before the static site", async () => {
  const app = await buildApp();
  const response = await app.inject({ url: "/s.js", headers: HTML });
  assert.equal(response.body, "snippet");
});

test("non-HTML and non-GET requests to unknown paths return a JSON 404", async () => {
  const app = await buildApp();
  const plain = await app.inject({ url: "/experiments/42" });
  assert.equal(plain.statusCode, 404);
  assert.deepEqual(plain.json(), { error: "Route not found: GET /experiments/42" });
  const withQuery = await app.inject({ url: "/experiments/42?token=secret" });
  assert.deepEqual(withQuery.json(), { error: "Route not found: GET /experiments/42" });
  const post = await app.inject({ method: "POST", url: "/experiments/42", headers: HTML });
  assert.equal(post.statusCode, 404);
});

test("hashed assets are cached immutable for one year", async () => {
  const app = await buildApp();
  const response = await app.inject({ url: "/assets/app-1a2b.js" });
  assert.equal(response.statusCode, 200);
  assert.equal(response.headers["cache-control"], "public, max-age=31536000, immutable");
});

test("only files below staticDir/assets are immutable, even inside an assets folder", async () => {
  const parent = path.join(mkdtempSync(path.join(tmpdir(), "web-app-kit-static-")), "assets");
  const app = await buildApp(undefined, createStaticDir(parent));
  const index = await app.inject({ url: "/index.html" });
  assert.equal(index.headers["cache-control"], "no-cache");
  const asset = await app.inject({ url: "/assets/app-1a2b.js" });
  assert.equal(asset.headers["cache-control"], "public, max-age=31536000, immutable");
});

test("server paths with a trailing slash are normalised", async () => {
  const app = await buildApp(["/api/", "/mcp/"]);
  const response = await app.inject({ url: "/mcp", headers: HTML });
  assert.equal(response.statusCode, 404);
});

test("missing index.html fails fast", async () => {
  const app = Fastify();
  const staticDir = mkdtempSync(path.join(tmpdir(), "web-app-kit-empty-"));
  await assert.rejects(registerStaticSite(app, { staticDir }), ConfigurationError);
});

test("dotfiles are denied", async () => {
  const app = await buildApp();
  const response = await app.inject({ url: "/.env", headers: HTML });
  assert.equal(response.statusCode, 403);
  assert.doesNotMatch(response.body, /SECRET/);
});

test("isServerPath matches exact, sub-path and query forms only", () => {
  assert.equal(isServerPath("/api"), true);
  assert.equal(isServerPath("/api/x"), true);
  assert.equal(isServerPath("/api?x=1"), true);
  assert.equal(isServerPath("/apix"), false);
  assert.equal(isServerPath("/s.js", ["/s.js"]), true);
  assert.equal(isServerPath("/api/x", ["/api/"]), true);
});
