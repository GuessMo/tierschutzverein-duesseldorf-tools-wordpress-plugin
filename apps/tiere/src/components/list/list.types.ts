import type { AnimalFilter, AnimalListItem, AnimalOptions, ListView, Resource } from "@/types";

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
  view: ListView;
  onViewChange: (view: ListView) => void;
  onResetFilter: () => void;
}

export interface AnimalCollectionProps {
  animals: AnimalListItem[];
  options: AnimalOptions;
  view: ListView;
}

export interface AnimalItemProps {
  animal: AnimalListItem;
  options: AnimalOptions;
}

export interface ViewToggleProps {
  view: ListView;
  onChange: (view: ListView) => void;
}
