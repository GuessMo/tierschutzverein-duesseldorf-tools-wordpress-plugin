import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandSeparator,
  CommandShortcut,
} from "@web-app-kit/frontend/command";
import type { CatalogEntry } from "../catalog.types";
import { COMMAND_IMPORT_PATH } from "../groups";

function CommandDefault() {
  return (
    <Command label="Befehle" className="max-w-sm rounded-lg ring-1 ring-foreground/10">
      <CommandInput placeholder="Befehl suchen" />
      <CommandList>
        <CommandEmpty>Kein Befehl gefunden.</CommandEmpty>
        <CommandGroup heading="Ebene">
          <CommandItem>
            Kopieren
            <CommandShortcut>⌘C</CommandShortcut>
          </CommandItem>
          <CommandItem>Duplizieren</CommandItem>
          <CommandItem disabled>Sperren</CommandItem>
        </CommandGroup>
        <CommandSeparator />
        <CommandGroup heading="Ansicht">
          <CommandItem>Raster ein</CommandItem>
        </CommandGroup>
      </CommandList>
    </Command>
  );
}

export const commandEntry: CatalogEntry = {
  slug: "command",
  name: "Command",
  group: "menus",
  status: "stable",
  description:
    "Durchsuchbare Liste mit Tastatursteuerung (cmdk). Basis für Befehlspaletten und " +
    "MultiCombobox. Benötigt das Paket cmdk in der App.",
  exports: [
    "Command",
    "CommandDialog",
    "CommandEmpty",
    "CommandGroup",
    "CommandInput",
    "CommandItem",
    "CommandList",
    "CommandSeparator",
    "CommandShortcut",
  ],
  variants: [
    {
      key: "default",
      title: "Liste mit Suche und Gruppen",
      description: "CommandDialog zeigt dieselbe Liste als modales Fenster (title, description).",
      Preview: CommandDefault,
    },
  ],
  states: [
    { name: "Markiert", description: "Pfeiltasten markieren mit Primär-Fläche und Innenring." },
    { name: "Deaktiviert", description: "disabled am Eintrag: übersprungen, halb transparent." },
    { name: "Leer", description: "CommandEmpty erscheint, wenn die Suche nichts findet." },
  ],
  previewMinHeight: "18rem",
  importPath: COMMAND_IMPORT_PATH,
};
