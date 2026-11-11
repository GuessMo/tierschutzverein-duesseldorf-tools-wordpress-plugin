import { Input } from "@web-app-kit/frontend/ui";
import type { TextFieldProps } from "./common.types";
import { Field } from "./Field";

export function TextField({ id, label, value, onValueChange, ...inputProps }: TextFieldProps) {
  return (
    <Field id={id} label={label}>
      <Input
        {...inputProps}
        id={id}
        value={value}
        onChange={(event) => onValueChange(event.target.value)}
      />
    </Field>
  );
}
