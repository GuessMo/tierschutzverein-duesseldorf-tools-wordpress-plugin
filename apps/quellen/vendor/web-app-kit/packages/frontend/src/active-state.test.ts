import assert from "node:assert/strict";
import { readFile } from "node:fs/promises";
import { test } from "node:test";
import { contrastRatio } from "./contrast.js";
import type { CssVariables } from "./contrast.types.js";
import { readCssBlock } from "./cssVariables.js";

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
    "--primary": "#d6006f",
    "--primary-foreground": "#ffffff",
    "--primary-hover": "#b8005f",
    "--primary-active": "#99004f",
    "--primary-subtle": "#fde7f2",
    "--primary-subtle-foreground": "oklch(0.145 0 0)",
  },
  dark: {
    "--primary": "#ff4fa3",
    "--primary-foreground": "oklch(0.205 0 0)",
    "--primary-hover": "#ff73b6",
    "--primary-active": "#ff96c8",
    "--primary-subtle": "#4a1630",
    "--primary-subtle-foreground": "oklch(0.985 0 0)",
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

function tokensFor(selector: string, primary: CssVariables): CssVariables {
  const theme = readCssBlock(themeCss, selector);
  return { ...theme, ...defaultRoles(theme), ...primary };
}

function fallbackTokensFor(selector: string): CssVariables {
  const theme = readCssBlock(themeCss, selector);
  const roles = defaultRoles(theme);
  const { "--primary": primary, "--primary-foreground": foreground } = readCssBlock(
    brandCss,
    selector,
  );
  return { ...theme, "--primary": primary, "--primary-foreground": foreground, ...roles };
}

const CASES = MODES.flatMap((mode) => [
  {
    label: `brand.css ${mode.name}`,
    tokens: tokensFor(mode.selector, readCssBlock(brandCss, mode.selector)),
  },
  {
    label: `theme.css defaults ${mode.name}`,
    tokens: fallbackTokensFor(mode.selector),
  },
  {
    label: `deep pink sample ${mode.name}`,
    tokens: tokensFor(mode.selector, DEEP_PINK_SAMPLE[mode.name]),
  },
]);

for (const { label, tokens } of CASES) {
  for (const [foreground, background, minimum] of PAIRS) {
    test(`interaction states, ${label}: ${foreground} on ${background} meets ${minimum}:1`, () => {
      assert.ok(tokens[foreground], `${foreground} is defined`);
      assert.ok(tokens[background], `${background} is defined`);
      const ratio = contrastRatio(tokens[foreground], tokens[background]);
      assert.ok(ratio >= minimum, `${ratio.toFixed(2)}:1`);
    });
  }
}
