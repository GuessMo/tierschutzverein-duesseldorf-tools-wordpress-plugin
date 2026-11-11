import { SelectField, TextField } from "@/components/common";
import type { MissingStatus } from "@/types";
import type { SectionProps } from "../editor.types";

export function MissingFields({ draft, options, onChange }: SectionProps) {
  const status = options.missing_statuses.find((item) => item.value === draft.missing_status);
  const fields = options.missing_fields.filter((field) => field.states.includes(draft.missing_status));
  const setField = (key: string, value: string) =>
    onChange({ missing: { ...draft.missing, [key]: value } });
  return (
    <>
      <div className="grid gap-4 sm:grid-cols-2">
        <SelectField
          id="animal-missing-status"
          label="Status der Meldung"
          value={draft.missing_status}
          options={options.missing_statuses}
          hint={status?.text}
          onValueChange={(value) => onChange({ missing_status: value as MissingStatus })}
        />
      </div>
      <div className="grid gap-4 sm:grid-cols-2">
        {fields.map((field) => (
          <TextField
            key={field.key}
            id={`animal-missing-${field.key}`}
            label={field.label}
            type={field.type}
            value={draft.missing[field.key] ?? ""}
            readOnly={field.readonly_states.includes(draft.missing_status)}
            onValueChange={(value) => setField(field.key, value)}
          />
        ))}
      </div>
    </>
  );
}
