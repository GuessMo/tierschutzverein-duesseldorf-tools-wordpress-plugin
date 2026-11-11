import {
  Label,
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
  ToggleGroup,
  ToggleGroupItem,
} from "@web-app-kit/frontend/ui";
import { SearchField } from "@/components/list";
import type { RelayedKind, RelayedMonth } from "@/types";
import type { RelayedFiltersProps } from "./relayed.types";

const KINDS: { value: string; label: string }[] = [
  { value: "all", label: "Alle" },
  { value: "listing", label: "Private Vermittlung" },
  { value: "sighting", label: "Sichtung" },
];

const ALL_MONTHS = "all";

function monthItems(months: RelayedMonth[]) {
  return [{ value: ALL_MONTHS, label: "Alle Monate" }, ...months];
}

export function RelayedFilters({ filter, months, onChange }: RelayedFiltersProps) {
  return (
    <div className="flex flex-wrap items-end gap-4">
      <ToggleGroup
        type="single"
        variant="outline"
        size="sm"
        label="Art der Nachricht"
        value={filter.kind || "all"}
        onValueChange={(next) =>
          next && onChange({ kind: (next === "all" ? "" : next) as RelayedKind })
        }
      >
        {KINDS.map((kind) => (
          <ToggleGroupItem key={kind.value} value={kind.value}>
            {kind.label}
          </ToggleGroupItem>
        ))}
      </ToggleGroup>
      <div className="flex min-w-44 flex-col gap-1.5">
        <Label htmlFor="relayed-month">Monat</Label>
        <Select
          value={filter.month || ALL_MONTHS}
          onValueChange={(next) => onChange({ month: next === ALL_MONTHS ? "" : next })}
        >
          <SelectTrigger id="relayed-month" className="w-full">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              {monthItems(months).map((month) => (
                <SelectItem key={month.value} value={month.value}>
                  {month.label}
                </SelectItem>
              ))}
            </SelectGroup>
          </SelectContent>
        </Select>
      </div>
      <div className="min-w-56 flex-1">
        <SearchField value={filter.search} onChange={(search) => onChange({ search })} />
      </div>
    </div>
  );
}
