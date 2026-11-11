import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@web-app-kit/frontend/ui";
import { Field } from "@/components/common";
import { KIND_OPTIONS } from "@/config/kinds";
import type { EntryKind } from "@/types";

export function KindSelect({
  value,
  onChange,
}: {
  value: EntryKind;
  onChange: (kind: EntryKind) => void;
}) {
  return (
    <Field id="entry-kind" label="Art">
      <Select value={value} onValueChange={(next) => onChange(next as EntryKind)}>
        <SelectTrigger id="entry-kind" className="w-full">
          <SelectValue />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            {KIND_OPTIONS.map(([kind, label]) => (
              <SelectItem key={kind} value={kind}>
                {label}
              </SelectItem>
            ))}
          </SelectGroup>
        </SelectContent>
      </Select>
    </Field>
  );
}
