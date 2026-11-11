import { Textarea } from "@web-app-kit/frontend/ui";
import { Field } from "@/components/common";

export function BodyField({
  value,
  onChange,
}: {
  value: string;
  onChange: (body: string) => void;
}) {
  return (
    <Field id="entry-body" label="Text">
      <Textarea
        id="entry-body"
        value={value}
        rows={6}
        maxLength={20000}
        onChange={(event) => onChange(event.target.value)}
      />
    </Field>
  );
}
