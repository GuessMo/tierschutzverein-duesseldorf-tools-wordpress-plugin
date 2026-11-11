export interface AlbIdentityConfig {
  albArn: string;
  issuer: string;
  clientId: string;
}

export interface AlbIdentity {
  id: string;
  name?: string;
  email?: string;
}

export type AlbIdentityResolver = (
  headers: Record<string, string | string[] | undefined>,
) => Promise<AlbIdentity | null>;
