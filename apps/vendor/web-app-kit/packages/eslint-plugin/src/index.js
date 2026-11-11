import packageJson from "../package.json" with { type: "json" };
import { createRecommendedConfig } from "./configs/index.js";
import { rules } from "./rules/index.js";

const plugin = {
  meta: { name: packageJson.name, version: packageJson.version },
  rules,
  configs: {},
};

plugin.configs.recommended = createRecommendedConfig(plugin);

export default plugin;
