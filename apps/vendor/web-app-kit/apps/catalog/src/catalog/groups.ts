import type { CatalogGroup, CatalogStatus } from "./catalog.types";

export const GROUP_ORDER: CatalogGroup[] = [
  "layout",
  "actions",
  "forms",
  "menus",
  "overlays",
  "toolbars",
];

export const GROUP_LABELS: Record<CatalogGroup, string> = {
  layout: "Rahmen und Layout",
  actions: "Aktionen und Anzeige",
  forms: "Formulare",
  menus: "Menüs und Auswahl",
  overlays: "Overlays",
  toolbars: "Werkzeugleisten",
};

export const STATUS_LABELS: Record<CatalogStatus, string> = {
  stable: "Stabil",
  beta: "Beta",
  deprecated: "Auslaufend",
};

export const UI_IMPORT_PATH = "@web-app-kit/frontend/ui";
export const COMMAND_IMPORT_PATH = "@web-app-kit/frontend/command";
