import assert from "node:assert/strict";
import { readFile } from "node:fs/promises";
import { test } from "node:test";
import { resolveColorMix } from "./colorMix.js";
import { contrastRatio } from "./contrast.js";
import type { CssVariables, ThemeMode } from "./contrast.types.js";
import { cascadeTokens, resolveCssValue } from "./cssVariables.js";

const TEXT_MINIMUM = 4.5;
const NON_TEXT_MINIMUM = 3;
const CHROMA_UPPER_BOUND = 0.09;
const brandCss = await readFile(new URL("./brand.css", import.meta.url), "utf8");
const themeCss = await readFile(new URL("./theme.css", import.meta.url), "utf8");
const MODES: readonly ThemeMode[] = ["light", "dark"];

const TEXT_SURFACES = ["--background", "--card", "--popover", "--muted", "--accent"];
const INPUT_SURFACES = ["--background", "--card", "--popover", "--muted"];
const RING_SURFACES = ["--background", "--card", "--popover", "--sidebar"];

const TEXT_PAIRS = [
  ...TEXT_SURFACES.map((surface) => ["--foreground", surface]),
  ...TEXT_SURFACES.map((surface) => ["--muted-foreground", surface]),
  ["--card-foreground", "--card"],
  ["--popover-foreground", "--popover"],
  ["--secondary-foreground", "--secondary"],
  ["--accent-foreground", "--accent"],
  ["--sidebar-foreground", "--sidebar"],
  ["--sidebar-accent-foreground", "--sidebar-accent"],
  ["--secondary-foreground", "--secondary-hover-default"],
  ["--secondary-foreground", "--secondary-active-default"],
  ["--tertiary-foreground-default", "--tertiary-default"],
  ["--tertiary-foreground-default", "--tertiary-hover-default"],
  ["--tertiary-active-foreground-default", "--tertiary-active-default"],
  ["--secondary-subtle-foreground-default", "--secondary-subtle-default"],
  ["--brand-foreground", "--brand"],
];

const NON_TEXT_PAIRS = [
  ...INPUT_SURFACES.map((surface) => ["--input", surface]),
  ...RING_SURFACES.map((surface) => ["--ring", surface]),
];

const PAIRS = [
  ...TEXT_PAIRS.map(([foreground, background]) => [foreground, background, TEXT_MINIMUM] as const),
  ...NON_TEXT_PAIRS.map(
    ([foreground, background]) => [foreground, background, NON_TEXT_MINIMUM] as const,
  ),
];

const TINTS = [
  { label: "default", hue: "0", chroma: "0" },
  { label: "warm", hue: "60", chroma: "0.012" },
  { label: "cool", hue: "250", chroma: "0.02" },
  { label: "deep pink", hue: "350", chroma: "0.02" },
];

const SINGLE_OVERRIDES: Record<ThemeMode, CssVariables> = {
  light: { "--background": "oklch(0.95 0.015 80)", "--muted-foreground": "oklch(0.45 0.02 80)" },
  dark: { "--background": "oklch(0.17 0.015 80)", "--muted-foreground": "oklch(0.8 0.02 80)" },
};

function tokensFor(mode: ThemeMode, overrides: CssVariables): CssVariables {
  return { ...cascadeTokens(themeCss, brandCss, mode), ...overrides };
}

function colorOf(tokens: CssVariables, name: string): string {
  return resolveColorMix(resolveCssValue(tokens[name], tokens));
}

function failingPairs(tokens: CssVariables): string[] {
  return PAIRS.flatMap(([foreground, background, minimum]) => {
    const ratio = contrastRatio(colorOf(tokens, foreground), colorOf(tokens, background));
    return ratio >= minimum ? [] : [`${foreground} on ${background}: ${ratio.toFixed(2)}:1`];
  });
}

function tintOverrides(hue: string, chroma: string): CssVariables {
  return { "--neutral-hue": hue, "--neutral-chroma": chroma };
}

for (const mode of MODES) {
  for (const { label, hue, chroma } of TINTS) {
    test(`neutral tint ${label} ${mode}: text and control pairs meet WCAG AA`, () => {
      assert.deepEqual(failingPairs(tokensFor(mode, tintOverrides(hue, chroma))), []);
    });
  }

  test(`neutral single overrides ${mode}: text and control pairs meet WCAG AA`, () => {
    const tokens = tokensFor(mode, { ...tintOverrides("80", "0.01"), ...SINGLE_OVERRIDES[mode] });
    assert.equal(tokens["--background"], SINGLE_OVERRIDES[mode]["--background"]);
    assert.deepEqual(failingPairs(tokens), []);
  });

  test(`neutral tint ${mode}: chroma ${CHROMA_UPPER_BOUND} passes for every hue`, () => {
    const failures = Array.from({ length: 360 }, (_, hue) =>
      failingPairs(tokensFor(mode, tintOverrides(String(hue), String(CHROMA_UPPER_BOUND)))),
    ).flat();
    assert.deepEqual(failures, []);
  });
}

test("neutral defaults resolve to exact grays without a seed in brand.css", () => {
  const tokens = cascadeTokens(themeCss, "", "light");
  assert.equal(resolveCssValue(tokens["--background"], tokens), "oklch(0.97 0 0)");
  assert.equal(resolveCssValue(tokens["--destructive"], tokens), "oklch(0.48 0.18 27.325)");
});
