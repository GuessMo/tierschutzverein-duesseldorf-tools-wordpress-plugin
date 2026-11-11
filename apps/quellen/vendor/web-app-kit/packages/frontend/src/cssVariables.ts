import type { CssVariables, ThemeMode } from "./contrast.types.js";

const DECLARATION_PATTERN = /(--[\w-]+)\s*:\s*([^;]+);/g;
const VAR_PATTERN = /var\(\s*(--[\w-]+)\s*(?:,\s*((?:[^()]|\([^()]*\))*))?\)/;
const MAX_SUBSTITUTIONS = 100;

class UnresolvedVariableError extends Error {
  constructor(expression: string) {
    super(`Cannot resolve CSS variable expression: ${expression}`);
    this.name = "UnresolvedVariableError";
  }
}

export function readCssDeclarations(css: string): Array<[string, string]> {
  return [...css.matchAll(DECLARATION_PATTERN)].map(([, name, value]) => [name, value.trim()]);
}

export function readCssBlock(css: string, selector: string): CssVariables {
  const start = css.indexOf(`${selector} {`);
  if (start === -1) return {};
  const end = css.indexOf("}", start);
  return Object.fromEntries(readCssDeclarations(css.slice(start, end)));
}

function substituteFirst(value: string, variables: CssVariables): string {
  const match = VAR_PATTERN.exec(value);
  if (!match) return value;
  const [expression, name, fallback] = match;
  const replacement = variables[name] ?? fallback?.trim();
  if (replacement === undefined) throw new UnresolvedVariableError(expression);
  return value.replace(expression, replacement);
}

export function resolveCssValue(value: string, variables: CssVariables): string {
  let resolved = value;
  for (let step = 0; step < MAX_SUBSTITUTIONS; step += 1) {
    if (!VAR_PATTERN.test(resolved)) return resolved;
    resolved = substituteFirst(resolved, variables);
  }
  throw new UnresolvedVariableError(value);
}

export function cascadeTokens(themeCss: string, brandCss: string, mode: ThemeMode): CssVariables {
  const themeLight = readCssBlock(themeCss, ":where(:root)");
  const brandLight = readCssBlock(brandCss, ":root");
  if (mode === "light") return { ...themeLight, ...brandLight };
  const themeDark = readCssBlock(themeCss, ":where(.dark)");
  const brandDark = readCssBlock(brandCss, ".dark");
  return { ...themeLight, ...themeDark, ...brandLight, ...brandDark };
}
