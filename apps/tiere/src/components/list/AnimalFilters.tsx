import { IconFilterOff } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import { SelectField, TextField } from "@/components/common";
import { ALL, POST_STATUS_LABELS } from "@/config/labels";
import { EMPTY_FILTER, isFilterActive, speciesOptions, stateOptions } from "@/lib";
import type { AnimalFilter, AnimalOptions, ValueLabel } from "@/types";
import type { AnimalFiltersProps } from "./list.types";

function withAll(label: string, options: ValueLabel[]): ValueLabel[] {
  return [{ value: ALL, label }, ...options];
}

function visibilityOptions(): ValueLabel[] {
  const entries = Object.entries(POST_STATUS_LABELS);
  return withAll("Alle außer Papierkorb", entries.map(([value, label]) => ({ value, label })));
}

function selects(options: AnimalOptions) {
  return [
    { key: "species", label: "Tierart", options: withAll("Alle Tierarten", speciesOptions(options)) },
    { key: "state", label: "Status", options: withAll("Alle Status", stateOptions(options)) },
    { key: "visibility", label: "Sichtbarkeit", options: visibilityOptions() },
  ] as const satisfies { key: keyof AnimalFilter; label: string; options: ValueLabel[] }[];
}

export function AnimalFilters({ options, filter, onChange }: AnimalFiltersProps) {
  return (
    <div className="grid grid-cols-[repeat(auto-fill,minmax(12rem,1fr))] items-end gap-3">
      <TextField
        id="filter-search"
        label="Suche"
        type="search"
        value={filter.search}
        placeholder="Name, Tier-ID, Rasse"
        onValueChange={(search) => onChange({ ...filter, search })}
      />
      {selects(options).map((select) => (
        <SelectField
          key={select.key}
          id={`filter-${select.key}`}
          label={select.label}
          value={filter[select.key]}
          options={select.options}
          onValueChange={(value) => onChange({ ...filter, [select.key]: value })}
        />
      ))}
      {isFilterActive(filter) && (
        <Button type="button" variant="ghost" onClick={() => onChange(EMPTY_FILTER)}>
          <IconFilterOff data-icon="inline-start" />
          Filter zurücksetzen
        </Button>
      )}
    </div>
  );
}
