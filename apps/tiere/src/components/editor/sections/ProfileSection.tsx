import { FormSection, SelectField, TextAreaField, TextField } from "@/components/common";
import type { ValueLabel } from "@/types";
import type { SectionProps } from "../editor.types";
import { SpeciesFields } from "./SpeciesFields";

const NO_COLOR = "";

function colorOptions(props: SectionProps): ValueLabel[] {
  const colors = props.options.colors.map((color) => ({ value: String(color.id), label: color.name }));
  return [{ value: NO_COLOR, label: "Keine Angabe" }, ...colors];
}

export function ProfileSection(props: SectionProps) {
  const { draft, onChange } = props;
  return (
    <FormSection id="profile" title="Steckbrief">
      <div className="grid gap-4 sm:grid-cols-2">
        <TextField
          id="animal-name"
          label="Name"
          value={draft.name}
          required
          maxLength={190}
          onValueChange={(name) => onChange({ name })}
        />
        <TextField
          id="animal-teo-id"
          label="Teo-ID"
          value={draft.teo_id}
          onValueChange={(teo_id) => onChange({ teo_id })}
        />
      </div>
      <SpeciesFields {...props} />
      <div className="grid gap-4 sm:grid-cols-2">
        <SelectField
          id="animal-color"
          label="Farbe"
          value={draft.color === null ? NO_COLOR : String(draft.color)}
          options={colorOptions(props)}
          onValueChange={(value) => onChange({ color: value === NO_COLOR ? null : Number(value) })}
        />
      </div>
      <TextAreaField
        id="animal-description"
        label="Beschreibung"
        rows={8}
        value={draft.description}
        onValueChange={(description) => onChange({ description })}
      />
    </FormSection>
  );
}
