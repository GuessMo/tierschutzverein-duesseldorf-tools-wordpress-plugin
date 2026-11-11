import type { AnimalListItem } from "@/types";

export function animalName(animal: AnimalListItem): string {
  return animal.name || "Ohne Namen";
}

export function speciesLine(animal: AnimalListItem): string {
  return [animal.species, animal.breed].filter(Boolean).join(" · ") || "–";
}
