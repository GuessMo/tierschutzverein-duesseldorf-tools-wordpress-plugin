import { FormSection, SelectField, TextField } from "@/components/common";
import type { AnimalDraft, OptionSetKey } from "@/types";
import type { SectionProps } from "../editor.types";

type OptionField = "life_stage" | "size_cm" | "gender" | "castrated" | "vaccinated" | "chipped";

const OPTION_FIELDS: { key: OptionField; label: string; set: OptionSetKey }[] = [
  { key: "life_stage", label: "Lebensabschnitt", set: "life_stage" },
  { key: "size_cm", label: "Größe", set: "size" },
  { key: "gender", label: "Geschlecht", set: "gender" },
  { key: "castrated", label: "Kastriert", set: "ternary" },
  { key: "vaccinated", label: "Geimpft", set: "ternary" },
  { key: "chipped", label: "Gechippt", set: "ternary" },
];

function BirthdayFields({ draft, options, onChange }: SectionProps) {
  return (
    <div className="grid gap-4 sm:grid-cols-2">
      <SelectField
        id="animal-birthday-status"
        label="Geburtstag"
        value={draft.birthday_status}
        options={options.option_sets.birthday_status}
        onValueChange={(birthday_status) => onChange({ birthday_status })}
      />
      <TextField
        id="animal-birthday"
        label={draft.birthday_status === "estimated" ? "Geschätztes Datum" : "Datum"}
        type="date"
        value={draft.birthday}
        onValueChange={(birthday) => onChange({ birthday })}
      />
    </div>
  );
}

export function DetailsSection(props: SectionProps) {
  const { draft, options, onChange } = props;
  return (
    <FormSection id="details" title="Alter, Größe und Gesundheit">
      <BirthdayFields {...props} />
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {OPTION_FIELDS.map((field) => (
          <SelectField
            key={field.key}
            id={`animal-${field.key}`}
            label={field.label}
            value={draft[field.key]}
            options={options.option_sets[field.set]}
            onValueChange={(value) => onChange({ [field.key]: value } as Partial<AnimalDraft>)}
          />
        ))}
      </div>
    </FormSection>
  );
}
