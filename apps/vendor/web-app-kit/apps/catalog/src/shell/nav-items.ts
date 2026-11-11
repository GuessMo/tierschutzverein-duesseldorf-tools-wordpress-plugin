import { CATALOG_ENTRIES } from "../catalog/entries";
import { componentPath, PATHS } from "../routing/paths";
import type { NavSection } from "./nav-items.types";

const componentLinks = [...CATALOG_ENTRIES]
  .sort((first, second) => first.name.localeCompare(second.name, "de"))
  .map((entry) => ({ label: entry.name, href: componentPath(entry.slug) }));

export const NAV_SECTIONS: NavSection[] = [
  { id: "start", label: "Start", links: [{ label: "Übersicht", href: PATHS.home }] },
  {
    id: "basics",
    label: "Grundlagen",
    links: [
      { label: "Farben", href: PATHS.colors },
      { label: "Radien", href: PATHS.radii },
    ],
  },
  {
    id: "components",
    label: "Komponenten",
    links: [{ label: "Alle Komponenten", href: PATHS.components }, ...componentLinks],
  },
  { id: "status", label: "Status", links: [{ label: "Statusübersicht", href: PATHS.status }] },
];
