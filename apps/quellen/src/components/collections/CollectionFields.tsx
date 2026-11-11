import { Textarea } from "@web-app-kit/frontend/ui";
import { Field, NameField } from "@/components/common";
import type { CollectionFieldsProps } from "./collections.types";

export function CollectionFields({ name, description, onChange }: CollectionFieldsProps) {
  return (
    <>
      <NameField
        id="collection-name"
        value={name}
        onValueChange={(next) => onChange({ name: next, description })}
      />
      <Field id="collection-description" label="Beschreibung (optional)">
        <Textarea
          id="collection-description"
          value={description}
          rows={3}
          maxLength={2000}
          onChange={(event) => onChange({ name, description: event.target.value })}
        />
      </Field>
    </>
  );
}
