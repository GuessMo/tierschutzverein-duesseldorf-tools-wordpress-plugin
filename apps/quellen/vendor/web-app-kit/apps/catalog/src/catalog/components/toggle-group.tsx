import { useState } from "react";
import { IconPointer, IconSquare, IconTypography } from "@tabler/icons-react";
import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewStack } from "../preview-layout";

const TOOLS = [
  { value: "select", label: "Auswählen", icon: IconPointer },
  { value: "frame", label: "Rahmen", icon: IconSquare },
  { value: "text", label: "Text", icon: IconTypography },
];

function ToggleGroupVariants() {
  const [tool, setTool] = useState("select");
  return (
    <PreviewStack>
      <ToggleGroup
        type="single"
        label="Werkzeug"
        value={tool}
        onValueChange={(next) => next && setTool(next)}
        variant="outline"
        size="icon"
      >
        {TOOLS.map(({ value, label, icon: Icon }) => (
          <ToggleGroupItem key={value} value={value} aria-label={label}>
            <Icon aria-hidden />
          </ToggleGroupItem>
        ))}
      </ToggleGroup>
      <ToggleGroup type="multiple" label="Ansicht" spacing={2} defaultValue={["grid"]}>
        <ToggleGroupItem value="grid">Raster</ToggleGroupItem>
        <ToggleGroupItem value="rulers">Lineale</ToggleGroupItem>
        <ToggleGroupItem value="guides">Hilfslinien</ToggleGroupItem>
      </ToggleGroup>
    </PreviewStack>
  );
}

export const toggleGroupEntry: CatalogEntry = {
  slug: "toggle-group",
  name: "ToggleGroup",
  group: "toolbars",
  status: "stable",
  description:
    "Gruppe von Toggles: single für ein aktives Werkzeug, multiple für unabhängige " +
    "Schalter. Ein Tab-Stopp, Pfeiltasten wechseln das Item.",
  exports: ["ToggleGroup", "ToggleGroupItem"],
  types: ["ToggleGroupProps", "ToggleGroupStyle", "ToggleGroupOrientation"],
  variants: [
    {
      key: "variants",
      title: "Werkzeugauswahl und Mehrfachschalter",
      description: "label ist Pflicht (oder aria-labelledby); bei single den leeren Wert abfangen.",
      Preview: ToggleGroupVariants,
    },
  ],
  states: [
    { name: "Aktiv", description: "Fläche --primary wie beim Toggle." },
    { name: "Fokus", description: "Ein Tab-Stopp, Fokus wandert mit den Pfeiltasten." },
    { name: "Deaktiviert", description: "Einzelnes Item oder ganze Gruppe gedämpft." },
  ],
};
