import jsxA11y from "eslint-plugin-jsx-a11y";
import {
  PARSER,
  TSX_PLUGINS,
  TYPESCRIPT_PLUGINS,
  createParserOptions,
} from "./parser-options.js";

const IGNORED_DIRECTORIES = ["**/dist/**", "**/node_modules/**", "**/vendor/**"];
const KIT_UI_SOURCES = ["**/packages/frontend/src/ui/**"];
const TYPESCRIPT_FILES = ["**/*.{ts,tsx}"];

const A11Y_RULES = {
  "jsx-a11y/no-static-element-interactions": "error",
  "jsx-a11y/click-events-have-key-events": "error",
  "jsx-a11y/interactive-supports-focus": "error",
  "jsx-a11y/no-noninteractive-element-interactions": "error",
};

function createLanguageBlock(name, files, babelPlugins) {
  return {
    name,
    files,
    languageOptions: { parser: PARSER, parserOptions: createParserOptions(babelPlugins) },
  };
}

export function createRecommendedConfig(plugin) {
  return [
    { name: "web-app-kit/ignores", ignores: IGNORED_DIRECTORIES },
    createLanguageBlock("web-app-kit/typescript", ["**/*.ts"], TYPESCRIPT_PLUGINS),
    createLanguageBlock("web-app-kit/tsx", ["**/*.tsx"], TSX_PLUGINS),
    {
      name: "web-app-kit/recommended",
      files: TYPESCRIPT_FILES,
      plugins: { "web-app-kit": plugin, "jsx-a11y": jsxA11y },
      rules: { "web-app-kit/interactive-states": "error", ...A11Y_RULES },
    },
    {
      name: "web-app-kit/no-foreign-ui",
      files: TYPESCRIPT_FILES,
      ignores: KIT_UI_SOURCES,
      rules: { "web-app-kit/no-foreign-ui": "error" },
    },
  ];
}
