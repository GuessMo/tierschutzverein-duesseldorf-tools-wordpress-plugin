import type { RelayedFilter, RelayedItem, RelayedMonth } from "@/types";

export interface RelayedFiltersProps {
  filter: RelayedFilter;
  months: RelayedMonth[];
  onChange: (patch: Partial<Omit<RelayedFilter, "page">>) => void;
}

export interface RelayedRowProps {
  item: RelayedItem;
}
