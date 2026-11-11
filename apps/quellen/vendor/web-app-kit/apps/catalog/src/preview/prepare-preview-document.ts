import { clearPreviewThemeChoice, readPreviewTheme } from "./preview-theme";

export function preparePreviewDocument(search: string) {
  const root = document.documentElement;
  root.dataset.catalogPreview = "";
  root.classList.remove("light", "dark");
  root.classList.add(readPreviewTheme(search));
  clearPreviewThemeChoice();
}
