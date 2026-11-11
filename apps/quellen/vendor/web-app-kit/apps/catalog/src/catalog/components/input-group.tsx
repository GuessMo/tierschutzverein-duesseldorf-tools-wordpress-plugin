import { IconSearch, IconX } from "@tabler/icons-react";
import {
  InputGroup,
  InputGroupAddon,
  InputGroupButton,
  InputGroupInput,
  InputGroupText,
  InputGroupTextarea,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function InputGroupDefault() {
  return (
    <InputGroup className="max-w-sm">
      <InputGroupAddon>
        <IconSearch aria-hidden />
      </InputGroupAddon>
      <InputGroupInput aria-label="Ebenen suchen" placeholder="Ebenen suchen" />
      <InputGroupAddon align="inline-end">
        <InputGroupButton size="icon-xs" aria-label="Suche leeren">
          <IconX aria-hidden />
        </InputGroupButton>
      </InputGroupAddon>
    </InputGroup>
  );
}

function InputGroupTextareaPreview() {
  return (
    <InputGroup className="max-w-sm">
      <InputGroupTextarea aria-label="Notiz" placeholder="Notiz zur Ebene" />
      <InputGroupAddon align="block-end">
        <InputGroupText>0 / 500</InputGroupText>
      </InputGroupAddon>
    </InputGroup>
  );
}

export const inputGroupEntry: CatalogEntry = {
  slug: "input-group",
  name: "InputGroup",
  group: "forms",
  status: "stable",
  description:
    "Eingabefeld mit Zusätzen davor, dahinter, darüber oder darunter: Icon, Text, Knopf. " +
    "Ein Klick auf einen Zusatz ohne Knopf fokussiert das Feld.",
  exports: [
    "InputGroup",
    "InputGroupAddon",
    "InputGroupButton",
    "InputGroupInput",
    "InputGroupText",
    "InputGroupTextarea",
  ],
  variants: [
    {
      key: "default",
      title: "Suche mit Icon und Leeren-Knopf",
      description: "align an InputGroupAddon: inline-start, inline-end, block-start, block-end.",
      Preview: InputGroupDefault,
    },
    {
      key: "textarea",
      title: "Textfeld mit Zähler darunter",
      description: "InputGroupTextarea mit Zusatz block-end.",
      Preview: InputGroupTextareaPreview,
    },
  ],
  states: [
    { name: "Fokus", description: "Rahmen und Halo der Gruppe folgen dem Fokus des Feldes." },
    { name: "Ungültig", description: "aria-invalid am Feld färbt den Rahmen der Gruppe." },
    { name: "Deaktiviert", description: "disabled am Feld blendet die ganze Gruppe ab." },
  ],
};
