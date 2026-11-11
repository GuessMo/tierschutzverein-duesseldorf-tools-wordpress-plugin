import { IconSettings } from "@tabler/icons-react";
import {
  Button,
  Input,
  Label,
  Popover,
  PopoverContent,
  PopoverDescription,
  PopoverHeader,
  PopoverTitle,
  PopoverTrigger,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function PopoverDefault() {
  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button variant="ghost" size="icon" aria-label="Raster-Einstellungen">
          <IconSettings aria-hidden />
        </Button>
      </PopoverTrigger>
      <PopoverContent align="start" className="flex flex-col gap-4">
        <PopoverHeader>
          <PopoverTitle>Raster</PopoverTitle>
          <PopoverDescription>Gilt für alle Zeichenflächen.</PopoverDescription>
        </PopoverHeader>
        <div className="flex flex-col gap-2">
          <Label htmlFor="popover-grid">Abstand in Pixel</Label>
          <Input id="popover-grid" type="number" defaultValue={8} />
        </div>
      </PopoverContent>
    </Popover>
  );
}

export const popoverEntry: CatalogEntry = {
  slug: "popover",
  name: "Popover",
  group: "overlays",
  status: "stable",
  description:
    "Nicht-modales Fenster neben einem Auslöser, z. B. für Werkzeug-Einstellungen. Für " +
    "Aktionen DropdownMenu, für modale Aufgaben Dialog.",
  exports: [
    "Popover",
    "PopoverTrigger",
    "PopoverContent",
    "PopoverAnchor",
    "PopoverHeader",
    "PopoverTitle",
    "PopoverDescription",
  ],
  variants: [
    {
      key: "default",
      title: "Einstellungen am Icon-Button",
      description: "align, side und sideOffset an PopoverContent steuern die Lage.",
      Preview: PopoverDefault,
    },
  ],
  states: [
    { name: "Geöffnet", description: "Trigger mit Primärrahmen und Innenring." },
    { name: "Geschlossen", description: "Escape oder Klick daneben, Fokus zurück zum Trigger." },
  ],
  previewMinHeight: "16rem",
};
