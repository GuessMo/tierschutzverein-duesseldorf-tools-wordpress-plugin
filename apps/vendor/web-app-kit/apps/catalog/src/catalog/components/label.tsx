import { Checkbox, Input, Label } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewStack } from "../preview-layout";

function LabelDefault() {
  return (
    <PreviewStack>
      <div className="flex flex-col gap-2">
        <Label htmlFor="label-mail">E-Mail für Benachrichtigungen</Label>
        <Input id="label-mail" type="email" />
      </div>
      <div className="flex items-center gap-2">
        <Checkbox id="label-terms" />
        <Label htmlFor="label-terms">Wöchentliche Zusammenfassung senden</Label>
      </div>
    </PreviewStack>
  );
}

export const labelEntry: CatalogEntry = {
  slug: "label",
  name: "Label",
  group: "forms",
  status: "stable",
  description:
    "Sichtbare Beschriftung eines Feldes. htmlFor zeigt auf die id des Feldes, ein Klick " +
    "auf das Label fokussiert das Feld.",
  exports: ["Label"],
  variants: [
    {
      key: "default",
      title: "Über dem Feld und neben der Checkbox",
      description: "Klick auf das Label fokussiert bzw. schaltet das zugehörige Feld.",
      Preview: LabelDefault,
    },
  ],
  states: [
    {
      name: "Deaktiviert",
      description: "Folgt dem Feld: gedämpft, wenn das Feld deaktiviert ist.",
    },
  ],
};
