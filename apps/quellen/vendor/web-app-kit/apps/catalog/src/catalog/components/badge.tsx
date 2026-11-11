import { Badge } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewRow } from "../preview-layout";

function BadgeVariants() {
  return (
    <PreviewRow>
      <Badge>Neu</Badge>
      <Badge variant="secondary">Entwurf</Badge>
      <Badge variant="outline">Archiviert</Badge>
      <Badge variant="ghost">Intern</Badge>
      <Badge variant="destructive">Fehler</Badge>
      <Badge variant="info">Beta</Badge>
      <Badge variant="warning">Veraltet</Badge>
    </PreviewRow>
  );
}

function BadgeLink() {
  return (
    <PreviewRow>
      <Badge asChild variant="outline">
        <a href="#badge">Als Link</a>
      </Badge>
      <Badge asChild variant="info">
        <a href="#badge">Hinweis als Link</a>
      </Badge>
      <Badge asChild variant="link">
        <a href="#badge">Textlink</a>
      </Badge>
    </PreviewRow>
  );
}

export const badgeEntry: CatalogEntry = {
  slug: "badge",
  name: "Badge",
  group: "actions",
  status: "stable",
  description: "Kurze Kennzeichnung für Status oder Kategorie, optional als Link.",
  exports: ["Badge"],
  variants: [
    {
      key: "variants",
      title: "Varianten",
      description: "default, secondary, outline, ghost, destructive, info, warning.",
      Preview: BadgeVariants,
    },
    {
      key: "link",
      title: "Als Link",
      description: "Mit asChild auf ein <a>; dann mit Hover, Gedrückt und Fokus.",
      Preview: BadgeLink,
    },
  ],
  states: [
    { name: "Statisch", description: "Ohne asChild reine Anzeige, keine Zustände." },
    { name: "Link", description: "Hover, Gedrückt und Fokus wie bei Button." },
  ],
};
