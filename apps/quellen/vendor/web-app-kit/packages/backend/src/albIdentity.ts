import { AlbJwtVerifier } from "aws-jwt-verify";
import { UnauthorizedError } from "./errors.js";
import type { AlbIdentity, AlbIdentityConfig, AlbIdentityResolver } from "./albIdentity.types.js";

export const ALB_IDENTITY_HEADER = "x-amzn-oidc-data";

type ClaimsVerifier = (token: string) => Promise<Record<string, unknown>>;

function readHeader(headers: Record<string, string | string[] | undefined>): string | undefined {
  const value = headers[ALB_IDENTITY_HEADER];
  return Array.isArray(value) ? value[0] : value;
}

function optionalString(value: unknown): string | undefined {
  return typeof value === "string" && value.length > 0 ? value : undefined;
}

export function toAlbIdentity(claims: Record<string, unknown>): AlbIdentity {
  const id = optionalString(claims.sub);
  if (!id) throw new UnauthorizedError("Identity token has no subject");
  return { id, name: optionalString(claims.name), email: optionalString(claims.email) };
}

export function createIdentityResolver(verify: ClaimsVerifier): AlbIdentityResolver {
  return async (headers) => {
    const token = readHeader(headers);
    if (!token) return null;
    const claims = await verify(token).catch(() => {
      throw new UnauthorizedError("Invalid identity token");
    });
    return toAlbIdentity(claims);
  };
}

export function createAlbIdentityResolver(config: AlbIdentityConfig | null): AlbIdentityResolver {
  if (!config) return async () => null;
  const verifier = AlbJwtVerifier.create(config);
  return createIdentityResolver((token) => verifier.verify(token));
}

const CONFIG_ENV_KEYS = ["ALB_ARN", "ALB_OIDC_ISSUER", "ALB_OIDC_CLIENT_ID"] as const;

export function albIdentityConfigFromEnv(
  env: Record<string, string | undefined> = process.env,
): AlbIdentityConfig | null {
  const [albArn, issuer, clientId] = CONFIG_ENV_KEYS.map((key) => env[key] || undefined);
  if (!albArn && !issuer && !clientId) return null;
  if (!albArn || !issuer || !clientId) {
    throw new Error(`Set all or none of ${CONFIG_ENV_KEYS.join(", ")}`);
  }
  return { albArn, issuer, clientId };
}
