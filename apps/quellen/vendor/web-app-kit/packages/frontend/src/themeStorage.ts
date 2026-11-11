import type { Theme } from "./ThemeProvider.js";

export function isTheme(value: unknown): value is Theme {
  return value === "light" || value === "dark" || value === "system";
}

export function readStoredTheme(storageKey: string, defaultTheme: Theme): Theme {
  try {
    const stored = localStorage.getItem(storageKey);
    return isTheme(stored) ? stored : defaultTheme;
  } catch {
    return defaultTheme;
  }
}

export function persistTheme(storageKey: string, theme: Theme): boolean {
  try {
    localStorage.setItem(storageKey, theme);
    return true;
  } catch {
    return false;
  }
}
