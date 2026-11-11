import { ALL } from "@/config/labels";
import type { AnimalFilter, AnimalListItem } from "@/types";

export const EMPTY_FILTER: AnimalFilter = {
  search: "",
  species: ALL,
  state: ALL,
  visibility: ALL,
};

export const MISSING_PREFIX = "vermisst:";

export function animalState(animal: AnimalListItem): string {
  if (animal.record_type === "vermisst") return `${MISSING_PREFIX}${animal.missing_status}`;
  return animal.adoption_status;
}

function matchesSearch(animal: AnimalListItem, search: string): boolean {
  const needle = search.trim().toLowerCase();
  if (!needle) return true;
  const haystack = [animal.name, animal.internal_id, animal.teo_id, animal.breed, animal.species];
  return haystack.join(" ").toLowerCase().includes(needle);
}

function matchesSelect(value: string, selected: string): boolean {
  return selected === ALL || value === selected;
}

function matchesVisibility(animal: AnimalListItem, visibility: string): boolean {
  if (visibility === ALL) return animal.status !== "trash";
  return animal.status === visibility;
}

export function filterAnimals(animals: AnimalListItem[], filter: AnimalFilter): AnimalListItem[] {
  return animals.filter(
    (animal) =>
      matchesSearch(animal, filter.search) &&
      (filter.species === ALL || animal.species_ids.includes(Number(filter.species))) &&
      matchesSelect(animalState(animal), filter.state) &&
      matchesVisibility(animal, filter.visibility),
  );
}

export function isFilterActive(filter: AnimalFilter): boolean {
  return (Object.keys(EMPTY_FILTER) as (keyof AnimalFilter)[]).some(
    (key) => filter[key] !== EMPTY_FILTER[key],
  );
}
