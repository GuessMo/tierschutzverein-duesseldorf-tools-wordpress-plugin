import { existsSync } from "node:fs";
import path from "node:path";
import type { FastifyInstance, FastifyReply, FastifyRequest } from "fastify";
import fastifyStatic from "@fastify/static";
import { ConfigurationError, NotFoundError } from "./errors.js";
import type { StaticSiteOptions } from "./staticSite.types.js";

const DEFAULT_SERVER_PATHS = ["/api"];
const IMMUTABLE_CACHE = "public, max-age=31536000, immutable";
const ASSETS_DIR = "assets";
const PAGE_METHODS = new Set(["GET", "HEAD"]);

function normalizeServerPath(serverPath: string): string {
  return serverPath.length > 1 ? serverPath.replace(/\/+$/, "") : serverPath;
}

function toPathname(url: string): string {
  return url.split("?")[0];
}

export function isServerPath(url: string, serverPaths: string[] = DEFAULT_SERVER_PATHS): boolean {
  const pathname = toPathname(url);
  return serverPaths
    .map(normalizeServerPath)
    .some((prefix) => pathname === prefix || pathname.startsWith(`${prefix}/`));
}

function isAppPageRequest(request: FastifyRequest, serverPaths: string[]): boolean {
  if (!PAGE_METHODS.has(request.method)) return false;
  if (isServerPath(request.url, serverPaths)) return false;
  return (request.headers.accept ?? "").includes("text/html");
}

function createCacheHeaderSetter(staticDir: string) {
  return (reply: FastifyReply, filePath: string): void => {
    const [firstSegment] = path.relative(staticDir, filePath).split(path.sep);
    if (firstSegment === ASSETS_DIR) {
      reply.header("cache-control", IMMUTABLE_CACHE);
      return;
    }
    if (path.basename(filePath) === "index.html") reply.header("cache-control", "no-cache");
  };
}

function assertIndexHtml(staticDir: string): void {
  if (existsSync(path.join(staticDir, "index.html"))) return;
  throw new ConfigurationError(`Static site index.html not found in ${staticDir}`);
}

export async function registerStaticSite(
  app: FastifyInstance,
  { staticDir, serverPaths = DEFAULT_SERVER_PATHS }: StaticSiteOptions,
): Promise<void> {
  assertIndexHtml(staticDir);
  await app.register(fastifyStatic, {
    root: staticDir,
    prefix: "/",
    dotfiles: "deny",
    setHeaders: createCacheHeaderSetter(path.resolve(staticDir)),
  });
  app.setNotFoundHandler(async (request, reply) => {
    if (isAppPageRequest(request, serverPaths)) return reply.sendFile("index.html");
    throw new NotFoundError("Route", `${request.method} ${toPathname(request.url)}`);
  });
}
