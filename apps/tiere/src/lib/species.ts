import type { AnimalOptions, RequirementSet, SpeciesOption } from "@/types";

function containsTerm(species: SpeciesOption, termIds: number[]): boolean {
  if (termIds.includes(species.id)) return true;
  return species.breeds.some((breed) => termIds.includes(breed.id));
}

export function selectedSpecies(
  all: SpeciesOption[],
  termIds: number[],
): SpeciesOption | undefined {
  return all.find((species) => containsTerm(species, termIds));
}

export function withSpecies(species: SpeciesOption | undefined): number[] {
  return species ? [species.id] : [];
}

export function toggleBreed(species: SpeciesOption, termIds: number[], breedId: number): number[] {
  const breeds = termIds.filter((id) => id !== species.id);
  const next = breeds.includes(breedId)
    ? breeds.filter((id) => id !== breedId)
    : [...breeds, breedId];
  return [species.id, ...next];
}

export function requirementSet(
  options: AnimalOptions,
  termIds: number[],
): RequirementSet | undefined {
  const species = selectedSpecies(options.species, termIds);
  if (!species) return undefined;
  const breedSlugs = species.breeds
    .filter((breed) => termIds.includes(breed.id))
    .map((breed) => breed.slug);
  const slug = [...breedSlugs, species.slug].find((key) => key in options.requirement_sets);
  return slug ? options.requirement_sets[slug] : undefined;
}

export function isDog(options: AnimalOptions, termIds: number[]): boolean {
  const species = selectedSpecies(options.species, termIds);
  return species ? ["hund", "dog"].includes(species.slug) : false;
}
