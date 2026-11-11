import type { ToolBootstrap } from "@/types";

export function readBootstrap(): ToolBootstrap {
  const bootstrap = window.tsvdToolBootstrap;
  if (!bootstrap) throw new Error("Startdaten fehlen. Öffne die Übersicht über die Website.");
  return bootstrap;
}
