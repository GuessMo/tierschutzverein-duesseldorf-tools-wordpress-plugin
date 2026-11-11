import assert from "node:assert/strict";
import { test } from "node:test";
import { cascadeTokens, readCssBlock, resolveCssValue } from "./cssVariables.js";

const THEME_CSS = `
:where(:root) {
    --neutral-hue: 0;
    --surface: oklch(0.97 0 var(--neutral-hue));
    --text: oklch(0.2 0 0);
}

:where(.dark) {
    --surface: oklch(0.2 0 var(--neutral-hue));
}
`;

const BRAND_CSS = `
:root {
    --neutral-hue: 60;
}

.dark {
    --text: oklch(0.9 0 0);
}
`;

test("readCssBlock reads declarations of the exact selector", () => {
  assert.deepEqual(readCssBlock(BRAND_CSS, ":root"), { "--neutral-hue": "60" });
  assert.deepEqual(readCssBlock(THEME_CSS, ":root"), {});
});

test("resolveCssValue substitutes nested variables", () => {
  const variables = { "--hue": "250", "--tint": "0.02 var(--hue)" };
  assert.equal(resolveCssValue("oklch(0.5 var(--tint))", variables), "oklch(0.5 0.02 250)");
});

test("resolveCssValue uses the fallback only when the variable is missing", () => {
  const variables = { "--set": "oklch(1 0 0)", "--default": "oklch(0 0 0)" };
  assert.equal(resolveCssValue("var(--set, var(--default))", variables), "oklch(1 0 0)");
  assert.equal(resolveCssValue("var(--missing, var(--default))", variables), "oklch(0 0 0)");
  assert.equal(resolveCssValue("var(--missing, oklch(0.5 0 0))", {}), "oklch(0.5 0 0)");
});

test("resolveCssValue rejects missing and cyclic variables", () => {
  assert.throws(() => resolveCssValue("var(--missing)", {}), /Cannot resolve/);
  assert.throws(() => resolveCssValue("var(--a)", { "--a": "var(--a)" }), /Cannot resolve/);
});

test("cascadeTokens lets brand values win over theme defaults in both modes", () => {
  const light = cascadeTokens(THEME_CSS, BRAND_CSS, "light");
  const dark = cascadeTokens(THEME_CSS, BRAND_CSS, "dark");
  assert.equal(resolveCssValue(light["--surface"], light), "oklch(0.97 0 60)");
  assert.equal(resolveCssValue(dark["--surface"], dark), "oklch(0.2 0 60)");
  assert.equal(light["--text"], "oklch(0.2 0 0)");
  assert.equal(dark["--text"], "oklch(0.9 0 0)");
});
