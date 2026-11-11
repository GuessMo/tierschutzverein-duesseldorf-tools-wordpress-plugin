import { Textarea } from "@web-app-kit/frontend/ui";
import type { TextAreaFieldProps } from "./common.types";
import { Field } from "./Field";

export function TextAreaField({ id, label, value, rows = 5, hint, onValueChange }: TextAreaFieldProps) {
  return (
    <Field id={id} label={label} hint={hint}>
      <Textarea
        id={id}
        value={value}
        rows={rows}
        onChange={(event) => onValueChange(event.target.value)}
      />
    </Field>
  );
}
