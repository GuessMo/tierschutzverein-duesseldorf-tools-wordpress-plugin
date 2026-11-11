import { IconSettings, IconUsers } from "@tabler/icons-react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function TabsExample({ variant }: { variant: "default" | "line" }) {
  return (
    <Tabs defaultValue="general" className="max-w-md">
      <TabsList variant={variant} aria-label="Projekt-Einstellungen">
        <TabsTrigger value="general">
          <IconSettings data-icon="inline-start" aria-hidden />
          Allgemein
        </TabsTrigger>
        <TabsTrigger value="members">
          <IconUsers data-icon="inline-start" aria-hidden />
          Mitglieder
        </TabsTrigger>
        <TabsTrigger value="billing" disabled>
          Abrechnung
        </TabsTrigger>
      </TabsList>
      <TabsContent value="general">Name, Beschreibung und Sichtbarkeit.</TabsContent>
      <TabsContent value="members">Wer am Projekt mitarbeitet.</TabsContent>
    </Tabs>
  );
}

function TabsDefault() {
  return <TabsExample variant="default" />;
}

function TabsLine() {
  return <TabsExample variant="line" />;
}

export const tabsEntry: CatalogEntry = {
  slug: "tabs",
  name: "Tabs",
  group: "menus",
  status: "stable",
  description:
    "Wechselt zwischen gleichrangigen Ansichten auf einer Seite. Ein Tab-Stopp, Pfeiltasten " +
    "wechseln den Tab; TabsList mit aria-label benennen.",
  exports: ["Tabs", "TabsList", "TabsTrigger", "TabsContent"],
  variants: [
    {
      key: "default",
      title: "default",
      description: "Aktiver Tab als Fläche --primary-subtle mit Primärrahmen und Innenring.",
      Preview: TabsDefault,
    },
    {
      key: "line",
      title: "line",
      description: "Aktiver Tab mit Unterstrich in --foreground, ohne Fläche.",
      Preview: TabsLine,
    },
  ],
  states: [
    { name: "Hover", description: "Inaktiver Tab bekommt die zarte Fläche --primary-subtle." },
    { name: "Aktiv", description: "Rahmen oder Unterstrich, nicht nur Farbe." },
    { name: "Fokus", description: "Outline und Halo in --ring." },
    { name: "Deaktiviert", description: "50 % Deckkraft, per Pfeiltaste übersprungen." },
  ],
};
