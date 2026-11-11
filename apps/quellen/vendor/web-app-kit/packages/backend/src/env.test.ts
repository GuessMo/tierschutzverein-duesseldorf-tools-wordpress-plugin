import assert from "node:assert/strict";
import { mkdtempSync, writeFileSync } from "node:fs";
import { tmpdir } from "node:os";
import path from "node:path";
import { test } from "node:test";
import { loadLocalEnv, parseCorsOrigins, requireEnv } from "./env.js";

const KEYS = ["KIT_TEST_ALPHA", "KIT_TEST_BETA", "KIT_TEST_REQUIRED"];

function createEnvDir(content: string) {
  const dir = mkdtempSync(path.join(tmpdir(), "web-app-kit-env-"));
  writeFileSync(path.join(dir, ".env"), content);
  return dir;
}

test.afterEach(() => {
  for (const key of KEYS) delete process.env[key];
});

test("loadLocalEnv reads the given file into process.env", () => {
  const dir = createEnvDir("KIT_TEST_ALPHA=one\nKIT_TEST_BETA=\"two words\"\n");
  loadLocalEnv(path.join(dir, ".env"));
  assert.equal(process.env.KIT_TEST_ALPHA, "one");
  assert.equal(process.env.KIT_TEST_BETA, "two words");
});

test("loadLocalEnv defaults to .env in the working directory", (context) => {
  const dir = createEnvDir("KIT_TEST_ALPHA=from-cwd\n");
  const previous = process.cwd();
  process.chdir(dir);
  context.after(() => process.chdir(previous));
  loadLocalEnv();
  assert.equal(process.env.KIT_TEST_ALPHA, "from-cwd");
});

test("loadLocalEnv ignores a missing file", () => {
  const dir = mkdtempSync(path.join(tmpdir(), "web-app-kit-env-"));
  assert.doesNotThrow(() => loadLocalEnv(path.join(dir, ".env")));
  assert.equal(process.env.KIT_TEST_ALPHA, undefined);
});

test("requireEnv returns the value of a set variable", () => {
  process.env.KIT_TEST_REQUIRED = "value";
  assert.equal(requireEnv("KIT_TEST_REQUIRED"), "value");
});

test("requireEnv fails fast for missing or empty variables", () => {
  const expected = { message: "Missing required environment variable: KIT_TEST_REQUIRED" };
  assert.throws(() => requireEnv("KIT_TEST_REQUIRED"), expected);
  process.env.KIT_TEST_REQUIRED = "";
  assert.throws(() => requireEnv("KIT_TEST_REQUIRED"), expected);
});

test("parseCorsOrigins splits and trims a comma-separated list", () => {
  assert.deepEqual(parseCorsOrigins("https://a.test, https://b.test ,http://c.test", []), [
    "https://a.test",
    "https://b.test",
    "http://c.test",
  ]);
});

test("parseCorsOrigins falls back for undefined or empty values", () => {
  const fallback = ["http://localhost:5173"];
  assert.equal(parseCorsOrigins(undefined, fallback), fallback);
  assert.equal(parseCorsOrigins("", fallback), fallback);
});
