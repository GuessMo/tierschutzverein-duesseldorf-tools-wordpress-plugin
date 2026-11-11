import assert from "node:assert/strict";
import { readFile } from "node:fs/promises";
import { test } from "node:test";
import { contrastRatio } from "./contrast.js";
import type { CssVariables, ThemeMode } from "./contrast.types.js";
import { cascadeTokens, readCssBlock, resolveCssValue } from "./cssVariables.js";

const TEXT_MINIMUM = 4.5;
const NON_TEXT_MINIMUM = 3;
const brandCss = await readFile(new URL("./brand.css", import.meta.url), "utf8");
const themeCss = await readFile(new URL("./theme.css", import.meta.url), "utf8");

const MODES = [
  { name: "light", selector: ":root" },
  { name: "dark", selector: ".dark" },
] as const;

const DEEP_PINK_SAMPLE = {
  light: {
    "--primary": "oklch(0.5668 0.2294 0.76)",
    "--primary-foreground": "oklch(1 0 0)",
    "--primary-hover": "oklch(0.5065 0.2051 0.57)",
    "--primary-active": "oklch(0.4423 0.1793 0.04)",
    "--primary-subtle": "oklch(0.9487 0.0279 345.89)",
    "--primary-subtle-foreground": "oklch(0.145 0 0)",
    "--ring": "oklch(0.5668 0.2294 0.76)",
  },
  dark: {
    "--primary": "oklch(0.695 0.2229 355.31)",
    "--primary-foreground": "oklch(0.205 0 0)",
    "--primary-hover": "oklch(0.7413 0.1836 352.6)",
    "--primary-active": "oklch(0.7968 0.1382 350.72)",
    "--primary-subtle": "oklch(0.2932 0.0848 352.61)",
    "--primary-subtle-foreground": "oklch(0.985 0 0)",
    "--ring": "oklch(0.695 0.2229 355.31)",
  },
} as const;

const TEXT_PAIRS = [
  ["--primary-foreground", "--primary"],
  ["--primary-foreground", "--primary-hover"],
  ["--primary-foreground", "--primary-active"],
  ["--primary-subtle-foreground", "--primary-subtle"],
  ["--foreground", "--primary-subtle"],
  ["--muted-foreground", "--primary-subtle"],
  ["--primary-hover", "--primary-subtle"],
  ["--primary", "--background"],
  ["--primary", "--card"],
  ["--primary-hover", "--background"],
  ["--primary-hover", "--card"],
  ["--primary-active", "--background"],
  ["--primary-active", "--card"],
] as const;

const NON_TEXT_PAIRS = [
  ["--primary", "--background"],
  ["--primary-hover", "--background"],
  ["--primary-active", "--background"],
  ["--primary", "--card"],
  ["--primary", "--popover"],
  ["--primary", "--primary-subtle"],
  ["--ring", "--background"],
  ["--ring", "--card"],
  ["--ring", "--popover"],
  ["--ring", "--sidebar"],
  ["--ring", "--primary-subtle"],
] as const;

const PAIRS = [
  ...TEXT_PAIRS.map(([foreground, background]) => [foreground, background, TEXT_MINIMUM] as const),
  ...NON_TEXT_PAIRS.map(
    ([foreground, background]) => [foreground, background, NON_TEXT_MINIMUM] as const,
  ),
];

const ROLES = [
  "--primary-hover",
  "--primary-active",
  "--primary-subtle",
  "--primary-subtle-foreground",
] as const;

function defaultRoles(theme: CssVariables): CssVariables {
  return Object.fromEntries(ROLES.map((role) => [role, theme[`${role}-default`]]));
}

function tokensFor(mode: ThemeMode, primary: CssVariables): CssVariables {
  const theme = cascadeTokens(themeCss, "", mode);
  return { ...theme, ...defaultRoles(theme), ...primary };
}

function fallbackTokensFor(mode: ThemeMode, selector: string): CssVariables {
  const theme = cascadeTokens(themeCss, "", mode);
  const roles = defaultRoles(theme);
  const brand = readCssBlock(brandCss, selector);
  const { "--primary": primary, "--primary-foreground": foreground, "--ring": ring } = brand;
  return {
    ...theme,
    "--primary": primary,
    "--primary-foreground": foreground,
    "--ring": ring,
    ...roles,
  };
}

const CASES = MODES.flatMap((mode) => [
  {
    label: `brand.css ${mode.name}`,
    tokens: tokensFor(mode.name, readCssBlock(brandCss, mode.selector)),
  },
  {
    label: `theme.css defaults ${mode.name}`,
    tokens: fallbackTokensFor(mode.name, mode.selector),
  },
  {
    label: `deep pink sample ${mode.name}`,
    tokens: tokensFor(mode.name, DEEP_PINK_SAMPLE[mode.name]),
  },
]);

for (const { label, tokens } of CASES) {
  for (const [foreground, background, minimum] of PAIRS) {
    test(`interaction states, ${label}: ${foreground} on ${background} meets ${minimum}:1`, () => {
      assert.ok(tokens[foreground], `${foreground} is defined`);
      assert.ok(tokens[background], `${background} is defined`);
      const ratio = contrastRatio(
        resolveCssValue(tokens[foreground], tokens),
        resolveCssValue(tokens[background], tokens),
      );
      assert.ok(ratio >= minimum, `${ratio.toFixed(2)}:1`);
    });
  }
}
