import { FormSection, TextField } from "@/components/common";
import type { InterestSectionProps } from "../editor.types";

export function InterestSection({ draft, record, onChange }: InterestSectionProps) {
  const interest = record?.interest;
  if (!interest || draft.interest_seed === null) return null;
  return (
    <FormSection
      id="interest"
      title="Interesse"
      description={`Formular: ${interest.form} · Gesamt: ${interest.total}`}
    >
      <div className="grid gap-4 sm:grid-cols-2">
        <TextField
          id="animal-interest-seed"
          label="Basiswert (redaktionell)"
          type="number"
          min={0}
          step={1}
          value={String(draft.interest_seed)}
          onValueChange={(value) => onChange({ interest_seed: Math.max(0, Number(value) || 0) })}
        />
      </div>
    </FormSection>
  );
}
