import { Skeleton } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function SkeletonCard() {
  return (
    <div className="flex items-center gap-4" aria-busy="true">
      <Skeleton className="size-12 rounded-full" />
      <div className="flex flex-col gap-2">
        <Skeleton className="h-4 w-56" />
        <Skeleton className="h-4 w-40" />
      </div>
    </div>
  );
}

export const skeletonEntry: CatalogEntry = {
  slug: "skeleton",
  name: "Skeleton",
  group: "actions",
  status: "stable",
  description:
    "Platzhalter in der Form des erwarteten Inhalts, solange er lädt. Dekorativ " +
    "(aria-hidden); den Ladezustand am Container per aria-busy oder Text melden.",
  exports: ["Skeleton"],
  variants: [
    {
      key: "card",
      title: "Avatar mit zwei Zeilen",
      description: "Größe und Form per className (h-*, w-*, size-*, rounded-*).",
      Preview: SkeletonCard,
    },
  ],
  states: [
    { name: "Laden", description: "Fläche --accent, pulsiert." },
    { name: "Reduzierte Bewegung", description: "prefers-reduced-motion: kein Pulsieren." },
  ],
};
