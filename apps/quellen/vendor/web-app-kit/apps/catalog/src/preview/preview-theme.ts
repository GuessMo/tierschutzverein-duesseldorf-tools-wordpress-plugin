import type { ResolvedTheme } from "@web-app-kit/frontend";

export const PREVIEW_THEME_STORAGE_KEY = "kit-catalog-preview-theme";

export function readPreviewTheme(search: string): ResolvedTheme {
  return new URLSearchParams(search).get("theme") === "dark" ? "dark" : "light";
}

export function clearPreviewThemeChoice(): boolean {
  try {
    localStorage.removeItem(PREVIEW_THEME_STORAGE_KEY);
    return true;
  } catch {
    return false;
  }
}
