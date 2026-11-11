import { after, describe, it } from "node:test";
import { RuleTester } from "eslint";
import { PARSER, TSX_PLUGINS, createParserOptions } from "../configs/index.js";

RuleTester.describe = describe;
RuleTester.it = it;
RuleTester.itOnly = it.only;
RuleTester.afterAll = after;

export function createRuleTester() {
  return new RuleTester({
    languageOptions: {
      parser: PARSER,
      parserOptions: createParserOptions(TSX_PLUGINS),
    },
  });
}
