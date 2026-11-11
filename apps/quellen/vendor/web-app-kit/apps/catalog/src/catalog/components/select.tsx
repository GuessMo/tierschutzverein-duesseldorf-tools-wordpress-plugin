import {
  Label,
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectSeparator,
  SelectTrigger,
  SelectValue,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewStack } from "../preview-layout";

function SelectDefault() {
  return (
    <PreviewStack>
      <div className="flex flex-col gap-2">
        <Label htmlFor="select-status">Status</Label>
        <Select defaultValue="draft">
          <SelectTrigger id="select-status" className="w-56">
            <SelectValue />
          </SelectTrigger>
          <SelectContent position="popper">
            <SelectGroup>
              <SelectLabel>In Arbeit</SelectLabel>
              <SelectItem value="draft">Entwurf</SelectItem>
              <SelectItem value="review">In Prüfung</SelectItem>
            </SelectGroup>
            <SelectSeparator />
            <SelectGroup>
              <SelectLabel>Abgeschlossen</SelectLabel>
              <SelectItem value="approved">Freigegeben</SelectItem>
              <SelectItem value="archived" disabled>
                Archiviert
              </SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
      </div>
      <div className="flex flex-col gap-2">
        <Label htmlFor="select-small">Kleine Größe, deaktiviert</Label>
        <Select defaultValue="de" disabled>
          <SelectTrigger id="select-small" size="sm" className="w-56">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem value="de">Deutsch</SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
      </div>
    </PreviewStack>
  );
}

export const selectEntry: CatalogEntry = {
  slug: "select",
  name: "Select",
  group: "forms",
  status: "stable",
  description:
    "Auswahl aus einer Liste. Items stehen immer in einer SelectGroup; das Label zeigt per " +
    "htmlFor auf die id des Triggers.",
  exports: [
    "Select",
    "SelectTrigger",
    "SelectValue",
    "SelectContent",
    "SelectGroup",
    "SelectItem",
    "SelectLabel",
    "SelectSeparator",
    "SelectScrollUpButton",
    "SelectScrollDownButton",
  ],
  variants: [
    {
      key: "default",
      title: "Gruppen, Größen, deaktiviert",
      description:
        'position="popper" öffnet unter dem Trigger, Standard ist "item-aligned"; size="sm" ' +
        "für dichte Werkzeugleisten.",
      Preview: SelectDefault,
    },
  ],
  states: [
    { name: "Hover", description: "Trigger mit zarter Primärfläche." },
    { name: "Geöffnet", description: "Primärrahmen mit Innenring am Trigger." },
    { name: "Markierter Eintrag", description: "Zarte Fläche plus innerer 2-px-Ring in --ring." },
    { name: "Deaktiviert", description: "Trigger oder Eintrag gedämpft, nicht wählbar." },
  ],
  previewMinHeight: "18rem",
};
