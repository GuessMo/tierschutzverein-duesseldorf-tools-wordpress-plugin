import { MultiCombobox } from "@web-app-kit/frontend/command";
import { Field, SelectField } from "@/components/common";
import { breedLabel, selectedSpecies, speciesOptions, withBreeds, withSpecies } from "@/lib";
import type { SpeciesOption } from "@/types";
import type { SectionProps } from "../editor.types";

const NO_SPECIES = "";

function BreedChoices({ species, draft, onChange }: SectionProps & { species: SpeciesOption }) {
  if (species.breeds.length === 0) return null;
  const label = breedLabel(species);
  const options = species.breeds.map((breed) => ({ value: String(breed.id), label: breed.name }));
  const value = species.breeds
    .filter((breed) => draft.breeds.includes(breed.id))
    .map((breed) => String(breed.id));
  return (
    <Field id="animal-breeds" label={label}>
      <MultiCombobox
        id="animal-breeds"
        options={options}
        value={value}
        onValueChange={(next) => onChange({ breeds: withBreeds(species, next.map(Number)) })}
        placeholder={`${label} auswählen`}
        searchPlaceholder={`${label} suchen`}
        emptyText={`Keine passende ${label} gefunden`}
        selectedLabel={`Gewählte ${label === "Art" ? "Arten" : "Rassen"}`}
        removeLabel={(name) => `${name} entfernen`}
      />
    </Field>
  );
}

export function SpeciesFields(props: SectionProps) {
  const { draft, options, onChange } = props;
  const species = selectedSpecies(options.species, draft.breeds);
  const choose = (value: string) => {
    const next = options.species.find((option) => String(option.id) === value);
    onChange({ breeds: withSpecies(next) });
  };
  return (
    <div className="grid gap-4 sm:grid-cols-2">
      <SelectField
        id="animal-species"
        label="Tierart"
        value={species ? String(species.id) : NO_SPECIES}
        options={[{ value: NO_SPECIES, label: "Bitte wählen" }, ...speciesOptions(options)]}
        onValueChange={choose}
      />
      {species && <BreedChoices {...props} species={species} />}
    </div>
  );
}
