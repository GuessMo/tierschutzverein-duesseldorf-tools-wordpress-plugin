import { IconDownload, IconPlus, IconTrash } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewRow } from "../preview-layout";

function ButtonVariants() {
  return (
    <PreviewRow>
      <Button>Speichern</Button>
      <Button variant="secondary">Entwurf</Button>
      <Button variant="tertiary">Vorschau</Button>
      <Button variant="outline">Abbrechen</Button>
      <Button variant="ghost">Zurücksetzen</Button>
      <Button variant="destructive">Löschen</Button>
      <Button variant="link">Mehr erfahren</Button>
    </PreviewRow>
  );
}

function ButtonSizes() {
  return (
    <PreviewRow>
      <Button size="xs">Klein xs</Button>
      <Button size="sm">Klein</Button>
      <Button>Standard</Button>
      <Button size="lg">Groß</Button>
      <Button size="icon" aria-label="Hinzufügen">
        <IconPlus aria-hidden />
      </Button>
      <Button size="icon-sm" variant="outline" aria-label="Herunterladen">
        <IconDownload aria-hidden />
      </Button>
    </PreviewRow>
  );
}

function ButtonStates() {
  return (
    <PreviewRow>
      <Button disabled>Deaktiviert</Button>
      <Button variant="outline" disabled>
        Deaktiviert
      </Button>
      <Button variant="destructive">
        <IconTrash data-icon="inline-start" aria-hidden />
        Projekt löschen
      </Button>
    </PreviewRow>
  );
}

export const buttonEntry: CatalogEntry = {
  slug: "button",
  name: "Button",
  group: "actions",
  status: "stable",
  description:
    "Löst eine Aktion aus. Pro Ansicht gibt es genau eine primäre Aktion, alles andere nutzt " +
    "secondary, tertiary, outline oder ghost.",
  exports: ["Button"],
  variants: [
    {
      key: "variants",
      title: "Farbvarianten",
      description: "default, secondary, tertiary, outline, ghost, destructive, link.",
      Preview: ButtonVariants,
    },
    {
      key: "sizes",
      title: "Größen",
      description: "xs und icon-xs sind 24px hoch (WCAG-Minimum); Icon-only braucht aria-label.",
      Preview: ButtonSizes,
    },
    {
      key: "states",
      title: "Deaktiviert und mit Icon",
      description: "Deaktiviert bleibt lesbar; Icons per data-icon am Textanfang oder -ende.",
      Preview: ButtonStates,
    },
  ],
  states: [
    { name: "Hover", description: "Primär --primary-hover, outline und ghost --primary-subtle." },
    { name: "Gedrückt", description: "Versatz um 1px, Primär: --primary-active." },
    { name: "Fokus", description: "Ring in --ring, auf gefüllten Flächen mit Abstand." },
    { name: "Deaktiviert", description: "50 % Deckkraft, Cursor not-allowed, nicht klickbar." },
  ],
};
