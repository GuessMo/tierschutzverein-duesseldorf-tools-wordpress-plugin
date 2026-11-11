import { useState } from "react";
import { IconCopy, IconDots, IconTrash } from "@tabler/icons-react";
import {
  Button,
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuRadioGroup,
  DropdownMenuRadioItem,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function DropdownMenuDefault() {
  const [isGridVisible, setGridVisible] = useState(true);
  const [sort, setSort] = useState("date");
  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="outline" size="icon" aria-label="Aktionen für Entwurf">
          <IconDots aria-hidden />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="start" className="w-56">
        <DropdownMenuGroup>
          <DropdownMenuItem>
            <IconCopy aria-hidden />
            Duplizieren
            <DropdownMenuShortcut>⌘D</DropdownMenuShortcut>
          </DropdownMenuItem>
          <DropdownMenuSub>
            <DropdownMenuSubTrigger>Verschieben nach</DropdownMenuSubTrigger>
            <DropdownMenuSubContent>
              <DropdownMenuItem>Projekt Herbst</DropdownMenuItem>
              <DropdownMenuItem>Projekt Winter</DropdownMenuItem>
            </DropdownMenuSubContent>
          </DropdownMenuSub>
        </DropdownMenuGroup>
        <DropdownMenuSeparator />
        <DropdownMenuCheckboxItem checked={isGridVisible} onCheckedChange={setGridVisible}>
          Raster anzeigen
        </DropdownMenuCheckboxItem>
        <DropdownMenuLabel>Sortierung</DropdownMenuLabel>
        <DropdownMenuRadioGroup value={sort} onValueChange={setSort}>
          <DropdownMenuRadioItem value="date">Nach Datum</DropdownMenuRadioItem>
          <DropdownMenuRadioItem value="name">Nach Name</DropdownMenuRadioItem>
        </DropdownMenuRadioGroup>
        <DropdownMenuSeparator />
        <DropdownMenuItem variant="destructive">
          <IconTrash aria-hidden />
          Löschen
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}

export const dropdownMenuEntry: CatalogEntry = {
  slug: "dropdown-menu",
  name: "DropdownMenu",
  group: "menus",
  status: "stable",
  description:
    "Menü mit Aktionen hinter einem Button. Trigger per asChild auf Button; Pfeiltasten " +
    "bewegen, Escape schließt, der Fokus kehrt zum Trigger zurück.",
  exports: [
    "DropdownMenu",
    "DropdownMenuTrigger",
    "DropdownMenuContent",
    "DropdownMenuGroup",
    "DropdownMenuItem",
    "DropdownMenuCheckboxItem",
    "DropdownMenuRadioGroup",
    "DropdownMenuRadioItem",
    "DropdownMenuLabel",
    "DropdownMenuSeparator",
    "DropdownMenuShortcut",
    "DropdownMenuSub",
    "DropdownMenuSubTrigger",
    "DropdownMenuSubContent",
    "DropdownMenuPortal",
  ],
  variants: [
    {
      key: "default",
      title: "Aktionen, Untermenü, Auswahl",
      description: "Item, Sub, CheckboxItem, RadioGroup und destruktiver Eintrag.",
      Preview: DropdownMenuDefault,
    },
  ],
  states: [
    { name: "Geöffnet", description: "Trigger mit Primärrahmen und Innenring." },
    { name: "Markierter Eintrag", description: "Zarte Fläche plus innerer 2-px-Ring in --ring." },
    { name: "Ausgewählt", description: "Haken bzw. Punkt bei Checkbox- und Radio-Einträgen." },
    { name: "Deaktiviert", description: "Eintrag gedämpft, nicht wählbar." },
  ],
  previewMinHeight: "22rem",
};
