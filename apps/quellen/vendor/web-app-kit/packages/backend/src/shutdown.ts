import type { FastifyInstance } from "fastify";
import type { ShutdownOptions, UnregisterShutdown } from "./shutdown.types.js";

const SHUTDOWN_SIGNALS: NodeJS.Signals[] = ["SIGTERM", "SIGINT"];
const DEFAULT_TIMEOUT_MS = 10_000;

async function closeAndExit(app: FastifyInstance, exit: (code: number) => void): Promise<void> {
  try {
    await app.close();
    exit(0);
  } catch (error) {
    app.log.error(error);
    exit(1);
  }
}

function startTimeout(app: FastifyInstance, timeoutMs: number, exit: (code: number) => void) {
  const timer = setTimeout(() => {
    app.log.error({ timeoutMs }, "Graceful shutdown timed out");
    exit(1);
  }, timeoutMs);
  timer.unref();
  return timer;
}

export function registerShutdown(
  app: FastifyInstance,
  {
    onClose,
    timeoutMs = DEFAULT_TIMEOUT_MS,
    exit = (code) => process.exit(code),
  }: ShutdownOptions = {},
): UnregisterShutdown {
  if (onClose) app.addHook("onClose", async () => onClose());
  const forceExit = () => exit(1);
  const unregister = () => {
    for (const signal of SHUTDOWN_SIGNALS) {
      process.off(signal, handleSignal);
      process.off(signal, forceExit);
    }
  };
  function handleSignal() {
    unregister();
    for (const signal of SHUTDOWN_SIGNALS) process.once(signal, forceExit);
    const timer = startTimeout(app, timeoutMs, exit);
    void closeAndExit(app, exit).finally(() => clearTimeout(timer));
  }
  for (const signal of SHUTDOWN_SIGNALS) process.on(signal, handleSignal);
  return unregister;
}
