export interface VersionRouteOptions {
  version: string | undefined;
  path?: string;
}

export interface VersionPayload {
  version: string | null;
  kitVersion: string;
}
