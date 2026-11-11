import type { ThemeVariableOverrides } from "./accentOverride.types.js";

const BRAND_VARIABLE = "--brand";
const OVERRIDE_STYLE_ID = "web-app-kit-theme-overrides";

export function applyAccentOverride(oklchValue: string | undefined): void {
  if (!oklchValue) return;
  document.documentElement.style.setProperty(BRAND_VARIABLE, oklchValue);
}

function toDeclarations(overrides: ThemeVariableOverrides, mode: "light" | "dark"): string {
  return Object.entries(overrides)
    .map(([name, values]) => `${name}: ${values[mode]};`)
    .join(" ");
}

export function applyThemeOverrides(overrides: ThemeVariableOverrides): void {
  if (Object.keys(overrides).length === 0) return;
  const existing = document.getElementById(OVERRIDE_STYLE_ID);
  const style = existing ?? document.createElement("style");
  style.id = OVERRIDE_STYLE_ID;
  style.textContent =
    `html:root { ${toDeclarations(overrides, "light")} } ` +
    `html:root.dark { ${toDeclarations(overrides, "dark")} }`;
  if (!existing) document.head.append(style);
}
