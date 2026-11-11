import babelParser from "@babel/eslint-parser";

export const PARSER = babelParser;

export const TYPESCRIPT_PLUGINS = ["typescript", "decorators"];
export const TSX_PLUGINS = ["jsx", "typescript", "decorators"];

export function createParserOptions(babelPlugins) {
  return {
    requireConfigFile: false,
    babelOptions: {
      babelrc: false,
      configFile: false,
      parserOpts: { plugins: babelPlugins },
    },
  };
}
