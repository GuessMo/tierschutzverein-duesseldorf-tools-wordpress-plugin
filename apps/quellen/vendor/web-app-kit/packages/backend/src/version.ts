import type { FastifyInstance } from "fastify";
import { KIT_VERSION } from "./kitVersion.js";
import type { VersionPayload, VersionRouteOptions } from "./version.types.js";

const DEFAULT_VERSION_PATH = "/api/version";
const VERSION_VARIABLE = "APP_VERSION";

export function readAppVersion(env: NodeJS.ProcessEnv = process.env): string | undefined {
  const value = env[VERSION_VARIABLE]?.trim();
  return value ? value : undefined;
}

export function registerVersionRoute(
  app: FastifyInstance,
  { version, path = DEFAULT_VERSION_PATH }: VersionRouteOptions,
): void {
  const payload: VersionPayload = {
    version: version ?? null,
    kitVersion: KIT_VERSION,
  };
  app.get(path, async (_request, reply) => {
    reply.header("cache-control", "no-store");
    return payload;
  });
}
