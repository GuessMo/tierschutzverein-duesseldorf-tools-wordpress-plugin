import { Badge, MetaRow } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewStack } from "../preview-layout";

function MetaRowLabelled() {
  return (
    <PreviewStack>
      <MetaRow label="Status">
        <Badge variant="info">Beta</Badge>
        <Badge variant="outline">v1.3.0</Badge>
        <Badge variant="warning">Ab 1.4.0 entfernt</Badge>
      </MetaRow>
    </PreviewStack>
  );
}

function MetaRowPlain() {
  return (
    <PreviewStack>
      <MetaRow>
        <Badge variant="secondary">Formulare</Badge>
        <Badge variant="outline">radix-nova</Badge>
        <Badge variant="ghost">Intern</Badge>
      </MetaRow>
    </PreviewStack>
  );
}

export const metaRowEntry: CatalogEntry = {
  slug: "meta-row",
  name: "MetaRow",
  group: "actions",
  status: "stable",
  description: "Umbrechende Reihe kleiner Meta-Angaben wie Status, Version oder Kategorie.",
  exports: ["MetaRow"],
  types: ["MetaRowProps"],
  variants: [
    {
      key: "label",
      title: "Mit Beschriftung",
      description: "label erscheint fett vor den Elementen und benennt die Gruppe.",
      Preview: MetaRowLabelled,
    },
    {
      key: "plain",
      title: "Ohne Beschriftung",
      description: "Reine Reihe ohne Gruppenrolle.",
      Preview: MetaRowPlain,
    },
  ],
  states: [{ name: "Statisch", description: "Reine Anzeige; Zustände tragen die Elemente." }],
};
