import { TextField } from "@/components/common";
import { EMPTY_FILTER, isFilterActive, withTerm } from "@/lib/filterEntries";
import { termsOf } from "@/lib/terms";
import type { EntryFiltersProps } from "./entries.types";
import { ResetFilterButton } from "./ResetFilterButton";
import { TaxonomyFilter } from "./TaxonomyFilter";

export function EntryFilters({ taxonomies, terms, filter, onChange }: EntryFiltersProps) {
  return (
    <div className="grid grid-cols-[repeat(auto-fill,minmax(11rem,1fr))] items-end gap-3">
      <TextField
        id="filter-search"
        label="Suche"
        type="search"
        value={filter.search}
        placeholder="Titel, Text, Herausgeber"
        onValueChange={(search) => onChange({ ...filter, search })}
      />
      {taxonomies.map((taxonomy) => (
        <TaxonomyFilter
          key={taxonomy.id}
          taxonomy={taxonomy}
          terms={termsOf(terms, taxonomy.id)}
          value={filter.termByTaxonomy[taxonomy.id]}
          onChange={(termId) => onChange(withTerm(filter, taxonomy.id, termId))}
        />
      ))}
      {isFilterActive(filter) && <ResetFilterButton onReset={() => onChange(EMPTY_FILTER)} />}
    </div>
  );
}
