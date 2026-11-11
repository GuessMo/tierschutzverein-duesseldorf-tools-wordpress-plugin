import { Label, Progress } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewStack } from "../preview-layout";

const STEPS = [
  { id: "progress-start", label: "Upload gestartet", value: 10 },
  { id: "progress-half", label: "Import zur Hälfte", value: 50 },
  { id: "progress-done", label: "Export fertig", value: 100 },
];

function ProgressValues() {
  return (
    <PreviewStack>
      {STEPS.map((step) => (
        <div key={step.id} className="flex flex-col gap-2">
          <Label id={step.id}>{step.label}</Label>
          <Progress aria-labelledby={step.id} value={step.value} />
        </div>
      ))}
    </PreviewStack>
  );
}

export const progressEntry: CatalogEntry = {
  slug: "progress",
  name: "Progress",
  group: "actions",
  status: "stable",
  description:
    "Fortschritt einer Aufgabe mit bekanntem Umfang (value, max). aria-label oder " +
    "aria-labelledby ist Pflicht; ohne value gilt die Leiste als unbestimmt.",
  exports: ["Progress"],
  types: ["ProgressProps"],
  variants: [
    {
      key: "values",
      title: "10 %, 50 %, 100 %",
      description: "Indikator --primary auf Spur --muted; role progressbar mit aria-value*.",
      Preview: ProgressValues,
    },
  ],
  states: [
    { name: "Läuft", description: "data-state loading, aria-valuenow gesetzt." },
    { name: "Fertig", description: "data-state complete bei value = max." },
    { name: "Unbestimmt", description: "value null: kein aria-valuenow." },
  ],
};
