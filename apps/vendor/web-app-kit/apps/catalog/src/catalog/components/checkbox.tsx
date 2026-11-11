import { Checkbox, Label } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewStack } from "../preview-layout";

const OPTIONS = [
  { id: "checkbox-off", label: "Aus", checked: false },
  { id: "checkbox-on", label: "An", checked: true },
  { id: "checkbox-mixed", label: "Teilweise", checked: "indeterminate" as const },
];

function CheckboxStates() {
  return (
    <PreviewStack>
      {OPTIONS.map((option) => (
        <div key={option.id} className="flex items-center gap-2">
          <Checkbox id={option.id} defaultChecked={option.checked} />
          <Label htmlFor={option.id}>{option.label}</Label>
        </div>
      ))}
      <div className="flex items-center gap-2">
        <Checkbox id="checkbox-disabled" defaultChecked disabled />
        <Label htmlFor="checkbox-disabled">Deaktiviert</Label>
      </div>
    </PreviewStack>
  );
}

export const checkboxEntry: CatalogEntry = {
  slug: "checkbox",
  name: "Checkbox",
  group: "forms",
  status: "stable",
  description:
    'An, aus oder teilweise ("indeterminate"). onCheckedChange liefert true, false oder ' +
    '"indeterminate"; Beschriftung per Label oder aria-label.',
  exports: ["Checkbox"],
  variants: [
    {
      key: "states",
      title: "Aus, an, teilweise, deaktiviert",
      description: "An und teilweise in --primary, Leertaste schaltet um.",
      Preview: CheckboxStates,
    },
  ],
  states: [
    { name: "Hover", description: "Rahmen in Primärfarbe, aus mit zarter Fläche." },
    { name: "Fokus", description: "Outline mit Abstand, auch auf gefüllter Fläche sichtbar." },
    { name: "An / teilweise", description: "Fläche --primary mit Haken bzw. Strich." },
    { name: "Deaktiviert", description: "50 % Deckkraft, nicht schaltbar." },
  ],
};
