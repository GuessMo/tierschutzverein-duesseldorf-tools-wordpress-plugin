import type { AnimalFilter, AnimalListItem, AnimalOptions, Resource } from "@/types";

export interface AnimalsScreenProps {
  options: AnimalOptions;
}

export interface AnimalFiltersProps {
  options: AnimalOptions;
  filter: AnimalFilter;
  onChange: (filter: AnimalFilter) => void;
}

export interface AnimalResultsProps {
  animals: Resource<AnimalListItem[]>;
  options: AnimalOptions;
  filter: AnimalFilter;
  onResetFilter: () => void;
}

export interface AnimalTableProps {
  animals: AnimalListItem[];
  options: AnimalOptions;
}

export interface AnimalRowProps {
  animal: AnimalListItem;
  options: AnimalOptions;
}
