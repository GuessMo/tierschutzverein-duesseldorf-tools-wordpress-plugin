import { PageContainer } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

const SIZES = [
  { size: "md", label: "md: Formulare, Lesetext (max. 48rem)" },
  { size: "lg", label: "lg: Listen (max. 80rem, Standard)" },
] as const;

function PageContainerSizes() {
  return (
    <div className="flex flex-col gap-3">
      {SIZES.map(({ size, label }) => (
        <PageContainer key={size} size={size} className="rounded-md border border-dashed py-3">
          <p className="text-sm">{label}</p>
        </PageContainer>
      ))}
    </div>
  );
}

export const pageContainerEntry: CatalogEntry = {
  slug: "page-container",
  name: "PageContainer",
  group: "layout",
  status: "stable",
  description: "Begrenzt Breite und Innenabstand des Seiteninhalts in der AppShell.",
  exports: ["PageContainer"],
  types: ["PageContainerProps", "PageContainerSize"],
  variants: [
    {
      key: "sizes",
      title: "Größen",
      description: 'size "md", "lg" (Standard) oder "full" für Arbeitsflächen ohne Grenze.',
      Preview: PageContainerSizes,
    },
  ],
  states: [{ name: "Statisch", description: "Reiner Layout-Baustein ohne Zustände." }],
};
