import assert from "node:assert/strict";
import { test, type TestContext } from "node:test";
import Fastify from "fastify";
import { registerShutdown } from "./shutdown.js";
import type { ShutdownOptions } from "./shutdown.types.js";

async function waitFor(isDone: () => boolean): Promise<void> {
  for (let attempt = 0; attempt < 100 && !isDone(); attempt++) {
    await new Promise((resolve) => setTimeout(resolve, 10));
  }
}

async function setup(t: TestContext, options: ShutdownOptions = {}) {
  const exitCodes: number[] = [];
  const app = Fastify();
  const unregister = registerShutdown(app, { ...options, exit: (code) => exitCodes.push(code) });
  t.after(unregister);
  await app.ready();
  return { app, exitCodes };
}

for (const signal of ["SIGTERM", "SIGINT"] as const) {
  test(`${signal} closes the app, runs onClose and exits 0`, async (t) => {
    let isHookCalled = false;
    const { exitCodes } = await setup(t, { onClose: () => void (isHookCalled = true) });
    process.emit(signal);
    await waitFor(() => exitCodes.length > 0);
    assert.deepEqual(exitCodes, [0]);
    assert.equal(isHookCalled, true);
  });
}

test("a failing close logs and exits 1", async (t) => {
  const { exitCodes } = await setup(t, {
    onClose: () => {
      throw new Error("disconnect failed");
    },
  });
  process.emit("SIGTERM");
  await waitFor(() => exitCodes.length > 0);
  assert.deepEqual(exitCodes, [1]);
});

test("a hanging close exits 1 after the timeout", async (t) => {
  const { exitCodes } = await setup(t, {
    timeoutMs: 30,
    onClose: () => new Promise(() => undefined),
  });
  process.emit("SIGTERM");
  await waitFor(() => exitCodes.length > 0);
  assert.deepEqual(exitCodes, [1]);
});

test("a second signal forces exit 1 and graceful listeners are removed", async (t) => {
  const listenersBefore = process.listenerCount("SIGINT");
  const { exitCodes } = await setup(t, { onClose: () => new Promise(() => undefined) });
  assert.equal(process.listenerCount("SIGINT"), listenersBefore + 1);
  process.emit("SIGTERM");
  process.emit("SIGINT");
  assert.deepEqual(exitCodes, [1]);
  assert.equal(process.listenerCount("SIGTERM"), listenersBefore + 1);
});
