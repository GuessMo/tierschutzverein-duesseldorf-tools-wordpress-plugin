import assert from "node:assert/strict";
import { test } from "node:test";
import { resolveColorMix } from "./colorMix.js";

test("resolveColorMix mixes two oklch colors in oklch space", () => {
  const mixed = resolveColorMix(
    "color-mix(in oklch, oklch(0.97 0.02 250), oklch(0.145 0.02 250) 8%)",
  );
  assert.equal(mixed, "oklch(0.904 0.02 250)");
});

test("resolveColorMix interpolates the hue along the shorter arc", () => {
  const mixed = resolveColorMix("color-mix(in oklch, oklch(0.5 0.1 350), oklch(0.5 0.1 10) 50%)");
  assert.equal(mixed, "oklch(0.5 0.1 0)");
});

test("resolveColorMix leaves other values unchanged", () => {
  assert.equal(resolveColorMix("oklch(0.5 0 0)"), "oklch(0.5 0 0)");
});
