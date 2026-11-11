import type { ReactNode } from "react";
import { findEntry } from "../catalog/entries";
import { componentSlugOf, PATHS } from "../routing/paths";
import { ColorsPage } from "./colors-page";
import { ComponentIndexPage } from "./component-index-page";
import { ComponentPage } from "./component-page";
import { HomePage } from "./home-page";
import { NotFoundPage } from "./not-found-page";
import { RadiiPage } from "./radii-page";
import { StatusPage } from "./status-page";

interface ResolvedPage {
  title: string;
  content: ReactNode;
}

const STATIC_PAGES: Record<string, () => ResolvedPage> = {
  [PATHS.home]: () => ({ title: "Übersicht", content: <HomePage /> }),
  [PATHS.colors]: () => ({ title: "Farben", content: <ColorsPage /> }),
  [PATHS.radii]: () => ({ title: "Radien", content: <RadiiPage /> }),
  [PATHS.components]: () => ({ title: "Komponenten", content: <ComponentIndexPage /> }),
  [PATHS.status]: () => ({ title: "Status", content: <StatusPage /> }),
};

export function resolvePage(path: string): ResolvedPage {
  const staticPage = STATIC_PAGES[path];
  if (staticPage) return staticPage();
  const entry = findEntry(componentSlugOf(path) ?? "");
  if (entry)
    return { title: entry.name, content: <ComponentPage key={entry.slug} entry={entry} /> };
  return { title: "Nicht gefunden", content: <NotFoundPage /> };
}
