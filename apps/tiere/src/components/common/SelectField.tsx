import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@web-app-kit/frontend/ui";
import type { SelectFieldProps } from "./common.types";
import { Field } from "./Field";

const EMPTY = "__empty";

function toItem(value: string): string {
  return value === "" ? EMPTY : value;
}

function fromItem(value: string): string {
  return value === EMPTY ? "" : value;
}

export function SelectField(props: SelectFieldProps) {
  const { id, label, value, options, hint, disabled, onValueChange } = props;
  return (
    <Field id={id} label={label} hint={hint}>
      <Select
        value={toItem(value)}
        disabled={disabled}
        onValueChange={(next) => onValueChange(fromItem(next))}
      >
        <SelectTrigger id={id} className="w-full">
          <SelectValue />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            {options.map((option) => (
              <SelectItem key={option.value} value={toItem(option.value)}>
                {option.label}
              </SelectItem>
            ))}
          </SelectGroup>
        </SelectContent>
      </Select>
    </Field>
  );
}
