import { Input, Label } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewStack } from "../preview-layout";

function InputDefault() {
  return (
    <PreviewStack>
      <div className="flex flex-col gap-2">
        <Label htmlFor="input-name">Projektname</Label>
        <Input id="input-name" defaultValue="Herbstkampagne" />
      </div>
      <div className="flex flex-col gap-2">
        <Label htmlFor="input-search">Suche</Label>
        <Input id="input-search" type="search" placeholder="Nach Entwürfen suchen" />
      </div>
    </PreviewStack>
  );
}

function InputStates() {
  return (
    <PreviewStack>
      <div className="flex flex-col gap-2">
        <Label htmlFor="input-error">Kürzel</Label>
        <Input id="input-error" defaultValue="x" aria-invalid aria-describedby="input-error-text" />
        <p id="input-error-text" className="text-sm text-destructive">
          Das Kürzel braucht mindestens zwei Zeichen.
        </p>
      </div>
      <div className="flex flex-col gap-2">
        <Label htmlFor="input-disabled">Projekt-ID</Label>
        <Input id="input-disabled" defaultValue="PRJ-2041" disabled />
      </div>
    </PreviewStack>
  );
}

export const inputEntry: CatalogEntry = {
  slug: "input",
  name: "Input",
  group: "forms",
  status: "stable",
  description:
    "Einzeiliges Eingabefeld. Jedes Feld hat ein sichtbares Label mit htmlFor auf die id; " +
    "ein Platzhalter ersetzt kein Label.",
  exports: ["Input"],
  variants: [
    {
      key: "default",
      title: "Mit Label",
      description: "Text- und Suchfeld mit sichtbarem Label.",
      Preview: InputDefault,
    },
    {
      key: "states",
      title: "Fehler und deaktiviert",
      description: "Fehler als Text, verbunden per aria-describedby, Feld mit aria-invalid.",
      Preview: InputStates,
    },
  ],
  states: [
    { name: "Fokus", description: "Rahmen und Ring in --ring." },
    { name: "Fehler", description: "aria-invalid färbt Rahmen und Ring in --destructive." },
    { name: "Deaktiviert", description: "50 % Deckkraft, nicht editierbar." },
  ],
};
