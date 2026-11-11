import assert from "node:assert/strict";
import { test } from "node:test";
import {
  albIdentityConfigFromEnv,
  createAlbIdentityResolver,
  createIdentityResolver,
} from "./albIdentity.js";
import { UnauthorizedError } from "./errors.js";

const header = (token: string) => ({ "x-amzn-oidc-data": token });

test("resolves identity from verified claims", async () => {
  const resolve = createIdentityResolver(async () => ({ sub: "u1", name: "Ada", email: "a@b.c" }));
  assert.deepEqual(await resolve(header("t")), { id: "u1", name: "Ada", email: "a@b.c" });
});

test("treats a missing header as anonymous", async () => {
  const resolve = createIdentityResolver(async () => ({ sub: "u1" }));
  assert.equal(await resolve({}), null);
});

test("rejects a token that fails verification", async () => {
  const resolve = createIdentityResolver(async () => {
    throw new Error("bad signature");
  });
  await assert.rejects(resolve(header("t")), UnauthorizedError);
});

test("rejects claims without subject", async () => {
  const resolve = createIdentityResolver(async () => ({ name: "Ada" }));
  await assert.rejects(resolve(header("t")), UnauthorizedError);
});

test("returns null identity when not configured (local dev)", async () => {
  assert.equal(await createAlbIdentityResolver(null)(header("t")), null);
});

test("config from env: none, all, partial", () => {
  assert.equal(albIdentityConfigFromEnv({}), null);
  const full = { ALB_ARN: "arn", ALB_OIDC_ISSUER: "iss", ALB_OIDC_CLIENT_ID: "cid" };
  const expected = { albArn: "arn", issuer: "iss", clientId: "cid" };
  assert.deepEqual(albIdentityConfigFromEnv(full), expected);
  assert.throws(() => albIdentityConfigFromEnv({ ALB_ARN: "arn" }));
});
