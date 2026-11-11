import { Input } from "@web-app-kit/frontend/ui";
import type { TextFieldProps } from "./common.types";
import { Field } from "./Field";

export function TextField(props: TextFieldProps) {
  const { id, label, value, hint, onValueChange, ...inputProps } = props;
  return (
    <Field id={id} label={label} hint={hint}>
      <Input
        {...inputProps}
        id={id}
        value={value}
        onChange={(event) => onValueChange(event.target.value)}
      />
    </Field>
  );
}
