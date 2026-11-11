import type { NameFieldProps } from "./common.types";
import { TextField } from "./TextField";

export function NameField({ id, value, onValueChange }: NameFieldProps) {
  return (
    <TextField
      id={id}
      label="Name"
      value={value}
      required
      maxLength={190}
      autoFocus
      onValueChange={onValueChange}
    />
  );
}
