import { useState } from "react";
import {
  ContextMenu,
  ContextMenuCheckboxItem,
  ContextMenuContent,
  ContextMenuGroup,
  ContextMenuItem,
  ContextMenuLabel,
  ContextMenuRadioGroup,
  ContextMenuRadioItem,
  ContextMenuSeparator,
  ContextMenuShortcut,
  ContextMenuSub,
  ContextMenuSubContent,
  ContextMenuSubTrigger,
  ContextMenuTrigger,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

const AREA_CLASSES =
  "flex h-32 max-w-sm items-center justify-center rounded-md border border-dashed " +
  "text-sm text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring " +
  "focus-visible:outline-hidden";

function ContextMenuDefault() {
  const [isLocked, setLocked] = useState(false);
  const [layer, setLayer] = useState("front");
  return (
    <ContextMenu>
      <ContextMenuTrigger tabIndex={0} className={AREA_CLASSES}>
        Rechtsklick, Shift+F10 oder Kontextmenü-Taste
      </ContextMenuTrigger>
      <ContextMenuContent className="w-56">
        <ContextMenuGroup>
          <ContextMenuItem>
            Kopieren
            <ContextMenuShortcut>⌘C</ContextMenuShortcut>
          </ContextMenuItem>
          <ContextMenuSub>
            <ContextMenuSubTrigger>Anordnen</ContextMenuSubTrigger>
            <ContextMenuSubContent>
              <ContextMenuItem>Nach vorne</ContextMenuItem>
              <ContextMenuItem>Nach hinten</ContextMenuItem>
            </ContextMenuSubContent>
          </ContextMenuSub>
        </ContextMenuGroup>
        <ContextMenuSeparator />
        <ContextMenuCheckboxItem checked={isLocked} onCheckedChange={setLocked}>
          Sperren
        </ContextMenuCheckboxItem>
        <ContextMenuLabel>Ebene</ContextMenuLabel>
        <ContextMenuRadioGroup value={layer} onValueChange={setLayer}>
          <ContextMenuRadioItem value="front">Vordergrund</ContextMenuRadioItem>
          <ContextMenuRadioItem value="back">Hintergrund</ContextMenuRadioItem>
        </ContextMenuRadioGroup>
      </ContextMenuContent>
    </ContextMenu>
  );
}

export const contextMenuEntry: CatalogEntry = {
  slug: "context-menu",
  name: "ContextMenu",
  group: "menus",
  status: "stable",
  description:
    "Kontextmenü per Rechtsklick. Trigger fokussierbar machen; jede Aktion zusätzlich ohne " +
    "Rechtsklick anbieten (Werkzeugleiste oder DropdownMenu).",
  exports: [
    "ContextMenu",
    "ContextMenuTrigger",
    "ContextMenuContent",
    "ContextMenuGroup",
    "ContextMenuItem",
    "ContextMenuCheckboxItem",
    "ContextMenuRadioGroup",
    "ContextMenuRadioItem",
    "ContextMenuLabel",
    "ContextMenuSeparator",
    "ContextMenuShortcut",
    "ContextMenuSub",
    "ContextMenuSubTrigger",
    "ContextMenuSubContent",
    "ContextMenuPortal",
  ],
  variants: [
    {
      key: "default",
      title: "Fokussierbare Fläche",
      description: "tabIndex={0} macht die Fläche per Tab erreichbar, dann Shift+F10.",
      Preview: ContextMenuDefault,
    },
  ],
  states: [
    { name: "Fokus", description: "Ring an der Fläche, wenn sie per Tab erreicht wird." },
    { name: "Markierter Eintrag", description: "Zarte Fläche plus innerer 2-px-Ring in --ring." },
    { name: "Ausgewählt", description: "Haken bzw. Punkt bei Checkbox- und Radio-Einträgen." },
  ],
  previewMinHeight: "20rem",
};
