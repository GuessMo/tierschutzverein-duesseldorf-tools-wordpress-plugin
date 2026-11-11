import assert from "node:assert/strict";
import { readFileSync } from "node:fs";
import { test } from "node:test";
import { contrastRatio } from "./contrast.js";
import { readCssBlock } from "./cssVariables.js";

const TEXT_MINIMUM = 4.5;
const NON_TEXT_MINIMUM = 3;
const brandCss = readFileSync(new URL("./brand.css", import.meta.url), "utf8");
const themeCss = readFileSync(new URL("./theme.css", import.meta.url), "utf8");

const MODES = [
  { name: "light", selector: ":root" },
  { name: "dark", selector: ".dark" },
] as const;

const PAIRS = [
  ["--primary-foreground", "--primary", TEXT_MINIMUM],
  ["--primary", "--background", NON_TEXT_MINIMUM],
  ["--ring", "--background", NON_TEXT_MINIMUM],
  ["--sidebar-primary-foreground", "--sidebar-primary", TEXT_MINIMUM],
  ["--sidebar-ring", "--sidebar", NON_TEXT_MINIMUM],
] as const;

for (const mode of MODES) {
  const tokens = {
    ...readCssBlock(themeCss, mode.selector),
    ...readCssBlock(brandCss, mode.selector),
  };
  for (const [foreground, background, minimum] of PAIRS) {
    test(`${mode.name}: ${foreground} on ${background} meets ${minimum}:1`, () => {
      const ratio = contrastRatio(tokens[foreground], tokens[background]);
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
