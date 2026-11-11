import { existsSync, readFileSync } from "node:fs";
import { resolve } from "node:path";
import { parseEnv } from "node:util";

export function loadLocalEnv(path = resolve(process.cwd(), ".env")): void {
  if (!existsSync(path)) return;

  Object.assign(process.env, parseEnv(readFileSync(path, "utf8")));
}

export function requireEnv(name: string): string {
  const value = process.env[name];
  if (!value) throw new Error(`Missing required environment variable: ${name}`);
  return value;
}

export function parseCorsOrigins(value: string | undefined, fallback: string[]): string[] {
  return value ? value.split(",").map((origin) => origin.trim()) : fallback;
}
