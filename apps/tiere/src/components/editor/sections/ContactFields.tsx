import { TextField } from "@/components/common";
import { CONTACT_FIELDS } from "@/config/labels";
import type { SectionProps } from "../editor.types";

export function ContactFields({ draft, onChange }: SectionProps) {
  const setField = (key: string, value: string) =>
    onChange({ private_contact: { ...draft.private_contact, [key]: value } });
  return (
    <fieldset className="flex flex-col">
      <legend className="mb-3 text-sm font-medium">Kontaktdaten des Halters</legend>
      <div className="grid gap-4 sm:grid-cols-2">
        {CONTACT_FIELDS.map((field) => (
          <TextField
            key={field.key}
            id={`animal-contact-${field.key}`}
            label={field.label}
            type={field.type}
            autoComplete={field.autoComplete}
            value={draft.private_contact[field.key] ?? ""}
            onValueChange={(value) => setField(field.key, value)}
          />
        ))}
      </div>
    </fieldset>
  );
}
