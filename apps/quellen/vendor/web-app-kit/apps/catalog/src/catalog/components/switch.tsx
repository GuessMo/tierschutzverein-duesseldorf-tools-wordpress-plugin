import { Label, Switch } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewStack } from "../preview-layout";

const OPTIONS = [
  { id: "switch-off", label: "Aus", checked: false, size: "default" as const },
  { id: "switch-on", label: "An", checked: true, size: "default" as const },
  { id: "switch-small", label: "Klein (sm)", checked: true, size: "sm" as const },
];

function SwitchStates() {
  return (
    <PreviewStack>
      {OPTIONS.map((option) => (
        <div key={option.id} className="flex items-center gap-2">
          <Switch id={option.id} size={option.size} defaultChecked={option.checked} />
          <Label htmlFor={option.id}>{option.label}</Label>
        </div>
      ))}
      <div className="flex items-center gap-2">
        <Switch id="switch-disabled" defaultChecked disabled />
        <Label htmlFor="switch-disabled">Deaktiviert</Label>
      </div>
    </PreviewStack>
  );
}

export const switchEntry: CatalogEntry = {
  slug: "switch",
  name: "Switch",
  group: "forms",
  status: "stable",
  description:
    "Schalter für Einstellungen, die sofort wirken (checked, onCheckedChange). Beschriftung " +
    "per Label oder aria-label; für Auswahl in Formularen mit Absenden die Checkbox.",
  exports: ["Switch"],
  variants: [
    {
      key: "states",
      title: "Aus, an, klein, deaktiviert",
      description: "size: default (32 × 18 px) oder sm (24 × 14 px); Trefferfläche größer.",
      Preview: SwitchStates,
    },
  ],
  states: [
    { name: "Aus", description: "Spur in --input, Daumen in --background." },
    { name: "Hover (aus)", description: "Rahmen in --primary." },
    { name: "Gedrückt (aus)", description: "Primärrahmen plus Innenring." },
    {
      name: "An",
      description: "Spur --primary, Hover --primary-hover, gedrückt --primary-active.",
    },
    { name: "Fokus", description: "Outline in --ring mit 2 px Abstand." },
    { name: "Deaktiviert", description: "50 % Deckkraft, nicht schaltbar." },
  ],
};
