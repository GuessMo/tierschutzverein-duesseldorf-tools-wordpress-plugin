import assert from "node:assert/strict";
import { readFileSync } from "node:fs";
import { test } from "node:test";
import { contrastRatio } from "./contrast.js";
import {
  cascadeTokens,
  readCssBlock,
  readCssDeclarations,
  resolveCssValue,
} from "./cssVariables.js";

const TEXT_MINIMUM = 4.5;
const NON_TEXT_MINIMUM = 3;
const brandCss = readFileSync(new URL("./brand.css", import.meta.url), "utf8");
const themeCss = readFileSync(new URL("./theme.css", import.meta.url), "utf8");

const FOREIGN_COLOR_PATTERNS = [
  /#[0-9a-f]{3,8}\b/i,
  /\b(?:rgba?|hsla?|hwb|lab|lch|oklab|color)\(/i,
  /color-mix\((?!\s*in oklch\b)/i,
];
const ALLOWED_KEYWORDS = new Set(["transparent", "currentcolor", "inherit", "none"]);

const MODES = [
  { name: "light", selector: ":root" },
  { name: "dark", selector: ".dark" },
] as const;

const PAIRS = [
  ["--primary-foreground", "--primary", TEXT_MINIMUM],
  ["--primary", "--background", NON_TEXT_MINIMUM],
  ["--ring", "--background", NON_TEXT_MINIMUM],
  ["--input", "--background", NON_TEXT_MINIMUM],
  ["--input", "--card", NON_TEXT_MINIMUM],
  ["--foreground", "--input", NON_TEXT_MINIMUM],
  ["--sidebar-primary-foreground", "--sidebar-primary", TEXT_MINIMUM],
  ["--sidebar-ring", "--sidebar", NON_TEXT_MINIMUM],
  ["--info-foreground", "--info", TEXT_MINIMUM],
  ["--info", "--background", TEXT_MINIMUM],
  ["--info", "--card", TEXT_MINIMUM],
  ["--warning-foreground", "--warning", TEXT_MINIMUM],
  ["--warning", "--background", TEXT_MINIMUM],
  ["--warning", "--card", TEXT_MINIMUM],
] as const;

const ROLE_PAIRS = [
  ["--secondary-foreground", "--secondary", TEXT_MINIMUM],
  ["--secondary-foreground", "--secondary-hover", TEXT_MINIMUM],
  ["--secondary-foreground", "--secondary-active", TEXT_MINIMUM],
  ["--secondary-subtle-foreground", "--secondary-subtle", TEXT_MINIMUM],
  ["--secondary", "--background", NON_TEXT_MINIMUM],
  ["--tertiary-foreground", "--tertiary", TEXT_MINIMUM],
  ["--tertiary-foreground", "--tertiary-hover", TEXT_MINIMUM],
  ["--tertiary-active-foreground", "--tertiary-active", TEXT_MINIMUM],
] as const;

for (const mode of MODES) {
  const brand = readCssBlock(brandCss, mode.selector);
  const tokens = cascadeTokens(themeCss, brandCss, mode.name);
  const rolePairs = ROLE_PAIRS.filter(
    ([foreground, background]) =>
      tokens[foreground] && tokens[background] && (brand[foreground] || brand[background]),
  );
  for (const [foreground, background, minimum] of [...PAIRS, ...rolePairs]) {
    test(`${mode.name}: ${foreground} on ${background} meets ${minimum}:1`, () => {
      const ratio = contrastRatio(
        resolveCssValue(tokens[foreground], tokens),
        resolveCssValue(tokens[background], tokens),
      );
      assert.ok(ratio >= minimum, `${ratio.toFixed(2)}:1`);
    });
  }
}

test("contrastRatio matches known reference values", () => {
  assert.equal(contrastRatio("#ffffff", "#000000").toFixed(2), "21.00");
  assert.equal(contrastRatio("#d6006f", "#ffffff").toFixed(2), "5.14");
  assert.equal(contrastRatio("oklch(1 0 0)", "oklch(0 0 0)").toFixed(2), "21.00");
});

test("contrastRatio rejects unsupported color formats", () => {
  assert.throws(() => contrastRatio("red", "#ffffff"), /Unsupported color/);
});

function isForeignColor(value: string): boolean {
  if (/^[a-z]+$/i.test(value)) return !ALLOWED_KEYWORDS.has(value.toLowerCase());
  return FOREIGN_COLOR_PATTERNS.some((pattern) => pattern.test(value));
}

test("isForeignColor accepts only oklch, var and color-mix in oklch", () => {
  const allowed = ["oklch(0.97 0 0)", "var(--x)", "color-mix(in oklch, var(--a), var(--b) 8%)"];
  const foreign = [
    "#d6006f",
    "rgb(0 0 0)",
    "hsl(0 0% 0%)",
    "red",
    "color-mix(in srgb, var(--a), var(--b))",
  ];
  assert.deepEqual(allowed.filter(isForeignColor), []);
  assert.deepEqual(foreign.filter((value) => !isForeignColor(value)), []);
});

for (const [file, css] of [["brand.css", brandCss], ["theme.css", themeCss]] as const) {
  test(`${file} declares colors only as oklch`, () => {
    const foreign = readCssDeclarations(css).filter(([, value]) => isForeignColor(value));
    assert.deepEqual(foreign, []);
  });
}
