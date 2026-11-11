import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import { HelpPopover } from "@/components/help";
import { RECORD_TYPES } from "@/config/labels";
import type { RecordType } from "@/types";
import type { RecordTypeSwitchProps } from "./editor.types";

export function RecordTypeSwitch({ value, onChange }: RecordTypeSwitchProps) {
  return (
    <div className="flex items-center gap-1">
    <ToggleGroup
      type="single"
      variant="outline"
      size="sm"
      label="Art des Eintrags"
      value={value}
      onValueChange={(next) => next && onChange(next as RecordType)}
    >
      {RECORD_TYPES.map((type) => (
        <ToggleGroupItem key={type.value} value={type.value}>
          {type.label}
        </ToggleGroupItem>
      ))}
    </ToggleGroup>
    <HelpPopover fieldId="animal-record-type" label="Art des Eintrags" />
    </div>
  );
}
