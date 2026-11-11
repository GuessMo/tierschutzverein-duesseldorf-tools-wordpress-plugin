import type { AnimalListItem, AnimalOptions, StatusOption, ValueLabel } from "@/types";
import { MISSING_PREFIX } from "./filterAnimals";

function missingOptions(options: AnimalOptions): StatusOption[] {
  return options.missing_statuses.map((status) => ({
    ...status,
    value: `${MISSING_PREFIX}${status.value}`,
    label: `Vermisst-Meldung: ${status.label}`,
  }));
}

export function stateOptions(options: AnimalOptions): StatusOption[] {
  return [...options.adoption_statuses, ...missingOptions(options)];
}

export function stateOf(
  animal: Pick<AnimalListItem, "record_type" | "missing_status" | "adoption_status">,
  options: AnimalOptions,
): StatusOption | undefined {
  if (animal.record_type === "vermisst") {
    return options.missing_statuses.find((status) => status.value === animal.missing_status);
  }
  return options.adoption_statuses.find((status) => status.value === animal.adoption_status);
}

export function speciesOptions(options: AnimalOptions): ValueLabel[] {
  return options.species.map((species) => ({ value: String(species.id), label: species.name }));
}
