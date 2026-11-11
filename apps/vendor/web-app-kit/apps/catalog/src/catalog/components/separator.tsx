import { Separator } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function SeparatorOrientations() {
  return (
    <div className="flex max-w-sm flex-col gap-4 text-sm">
      <p>Oberer Abschnitt</p>
      <Separator />
      <div className="flex h-5 items-center gap-4">
        <span>Entwürfe</span>
        <Separator orientation="vertical" />
        <span>Freigaben</span>
        <Separator orientation="vertical" />
        <span>Archiv</span>
      </div>
    </div>
  );
}

export const separatorEntry: CatalogEntry = {
  slug: "separator",
  name: "Separator",
  group: "forms",
  status: "stable",
  description:
    "Trennlinie zwischen Abschnitten. Standardmäßig dekorativ; decorative={false} nur, wenn " +
    "die Trennung Bedeutung trägt.",
  exports: ["Separator"],
  variants: [
    {
      key: "orientations",
      title: "Horizontal und vertikal",
      description: 'orientation="horizontal" (Standard) oder "vertical".',
      Preview: SeparatorOrientations,
    },
  ],
  states: [{ name: "Statisch", description: "Keine Interaktion, keine Zustände." }],
};
