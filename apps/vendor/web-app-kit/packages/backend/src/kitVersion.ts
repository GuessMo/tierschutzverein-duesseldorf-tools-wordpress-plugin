import packageJson from "../package.json" with { type: "json" };

export const KIT_VERSION: string = packageJson.version;
