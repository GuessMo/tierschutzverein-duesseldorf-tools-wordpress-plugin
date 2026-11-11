import type { CssVariables } from "./contrast.types.js";

const DECLARATION_PATTERN = /(--[\w-]+)\s*:\s*([^;]+);/g;

export function readCssBlock(css: string, selector: string): CssVariables {
  const start = css.indexOf(`${selector} {`);
  if (start === -1) return {};
  const end = css.indexOf("}", start);
  const block = css.slice(start, end);
  return Object.fromEntries(
    [...block.matchAll(DECLARATION_PATTERN)].map(([, name, value]) => [name, value.trim()]),
  );
}
