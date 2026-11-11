import { IconBooks, IconChartBar, IconMessages, IconPaw, IconWorld } from "@tabler/icons-react";
import { AppLauncher } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

const ITEMS = [
  { id: "website", label: "Website", href: "#website", icon: <IconWorld stroke={1.5} /> },
  { id: "tiere", label: "Tierverwaltung", href: "#tiere", icon: <IconPaw stroke={1.5} /> },
  { id: "anfragen", label: "Anfragen", href: "#anfragen", icon: <IconMessages stroke={1.5} /> },
  { id: "quellen", label: "Quellen", href: "#quellen", icon: <IconBooks stroke={1.5} /> },
  { id: "statistik", label: "Statistik", href: "#statistik", icon: <IconChartBar stroke={1.5} /> },
];

function AppLauncherDefault() {
  return (
    <div className="flex justify-end">
      <AppLauncher items={ITEMS} label="Tools" newTabHint="(öffnet in neuem Tab)" />
    </div>
  );
}

export const appLauncherEntry: CatalogEntry = {
  slug: "app-launcher",
  name: "AppLauncher",
  group: "menus",
  status: "beta",
  description:
    "Tool-Auswahl wie bei Google Workspace: Punkte-Raster oben rechts in AppShell-controls, " +
    "Popup mit einem Raster aus Tools, jedes Tool öffnet in neuem Tab. Optional.",
  exports: ["AppLauncher"],
  types: ["AppLauncherItem", "AppLauncherProps"],
  variants: [
    {
      key: "default",
      title: "Punkte-Raster mit Tools",
      description: "items mit id, label, href und Tabler-Icon; label und newTabHint per Props.",
      Preview: AppLauncherDefault,
    },
  ],
  states: [
    { name: "Geöffnet", description: "Raster mit drei Spalten, Fokus wandert ins Popup." },
    {
      name: "Eintrag",
      description: "Hover, gedrückt und Fokusring aus INTERACTIVE_SURFACE_CLASSES.",
    },
    { name: "Geschlossen", description: "Escape oder Klick daneben, Fokus zurück zum Trigger." },
  ],
  previewMinHeight: "20rem",
};
