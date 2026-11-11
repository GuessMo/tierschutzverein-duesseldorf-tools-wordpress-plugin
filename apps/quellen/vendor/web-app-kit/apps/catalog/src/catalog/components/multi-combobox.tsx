import { useState } from "react";
import { MultiCombobox } from "@web-app-kit/frontend/command";
import { Label } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { COMMAND_IMPORT_PATH } from "../groups";

const CATEGORIES = [
  { value: "462323286331", label: "Gasgrills" },
  { value: "462353727803", label: "Ersatzteile" },
  { value: "463160377659", label: "Smoker" },
  { value: "462320501051", label: "Pizzaöfen" },
  { value: "647748616565", label: "Feuerschalen" },
];

function MultiComboboxDefault() {
  const [value, setValue] = useState<string[]>(["462353727803"]);
  return (
    <div className="flex max-w-sm flex-col gap-2">
      <Label htmlFor="catalog-categories">Kategorien ohne Bewertungen</Label>
      <MultiCombobox
        id="catalog-categories"
        options={CATEGORIES}
        value={value}
        onValueChange={setValue}
        placeholder="Kategorie wählen"
        searchPlaceholder="Kategorie suchen"
        emptyText="Keine Kategorie gefunden."
        selectedLabel="Ausgewählte Kategorien"
        removeLabel={(label) => `${label} entfernen`}
      />
    </div>
  );
}

export const multiComboboxEntry: CatalogEntry = {
  slug: "multi-combobox",
  name: "MultiCombobox",
  group: "forms",
  status: "stable",
  description:
    "Mehrfachauswahl aus einer durchsuchbaren Liste; die Auswahl steht als entfernbare " +
    "Chips darunter. Werte ohne Option erscheinen mit ihrem Rohwert. Benötigt cmdk.",
  exports: ["MultiCombobox"],
  variants: [
    {
      key: "default",
      title: "Kategorien wählen",
      description: "Alle Texte per Props; removeLabel benennt den Entfernen-Knopf je Chip.",
      Preview: MultiComboboxDefault,
    },
  ],
  states: [
    { name: "Geöffnet", description: "Trigger mit Primärrahmen, Fokus im Suchfeld." },
    { name: "Ausgewählt", description: "Haken am Eintrag, Chip unter dem Feld." },
    { name: "Deaktiviert", description: "Trigger und Entfernen-Knöpfe gesperrt." },
    { name: "Geschlossen", description: "Escape oder Klick daneben, Fokus zurück zum Trigger." },
  ],
  previewMinHeight: "20rem",
  importPath: COMMAND_IMPORT_PATH,
};
