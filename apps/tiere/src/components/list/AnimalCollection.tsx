import { AnimalListRow } from "./AnimalListRow";
import { AnimalTile } from "./AnimalTile";
import type { AnimalCollectionProps } from "./list.types";

export function AnimalCollection({ animals, options, view }: AnimalCollectionProps) {
  if (view === "tiles") {
    return (
      <ul className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        {animals.map((animal) => (
          <li key={animal.id}>
            <AnimalTile animal={animal} options={options} />
          </li>
        ))}
      </ul>
    );
  }
  return (
    <ul className="flex flex-col gap-1">
      {animals.map((animal) => (
        <li key={animal.id}>
          <AnimalListRow animal={animal} options={options} />
        </li>
      ))}
    </ul>
  );
}
