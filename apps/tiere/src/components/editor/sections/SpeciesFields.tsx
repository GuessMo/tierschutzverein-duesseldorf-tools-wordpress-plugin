import { CheckboxField, SelectField } from "@/components/common";
import { selectedSpecies, speciesOptions, toggleBreed, withSpecies } from "@/lib";
import type { SpeciesOption } from "@/types";
import type { SectionProps } from "../editor.types";

const NO_SPECIES = "";

function BreedChoices({ species, draft, onChange }: SectionProps & { species: SpeciesOption }) {
  if (species.breeds.length === 0) return null;
  return (
    <fieldset className="flex flex-col gap-3">
      <legend className="mb-3 text-sm font-medium">Rasse</legend>
      <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {species.breeds.map((breed) => (
          <CheckboxField
            key={breed.id}
            id={`animal-breed-${breed.id}`}
            label={breed.name}
            checked={draft.breeds.includes(breed.id)}
            onCheckedChange={() => onChange({ breeds: toggleBreed(species, draft.breeds, breed.id) })}
          />
        ))}
      </div>
    </fieldset>
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
    <>
      <div className="grid gap-4 sm:grid-cols-2">
        <SelectField
          id="animal-species"
          label="Tierart"
          value={species ? String(species.id) : NO_SPECIES}
          options={[{ value: NO_SPECIES, label: "Bitte wählen" }, ...speciesOptions(options)]}
          onValueChange={choose}
        />
      </div>
      {species && <BreedChoices {...props} species={species} />}
    </>
  );
}
