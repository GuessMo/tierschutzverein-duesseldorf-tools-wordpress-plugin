import type { SplitDefinition } from "@/types/catalog";
import type { SplitId } from "@/types/document";

export const SPLITS: SplitDefinition[] = [
  { id: "full", label: "100", columns: [1] },
  { id: "half", label: "50 / 50", columns: [1, 1] },
  { id: "thirds", label: "33 / 33 / 33", columns: [1, 1, 1] },
  { id: "oneTwo", label: "33 / 66", columns: [1, 2] },
  { id: "twoOne", label: "66 / 33", columns: [2, 1] },
  { id: "quarters", label: "25 × 4", columns: [1, 1, 1, 1] },
];

export function findSplit(splitId: SplitId): SplitDefinition {
  return SPLITS.find((split) => split.id === splitId) ?? SPLITS[0];
}

export function toGridColumns(splitId: SplitId): string {
  return findSplit(splitId)
    .columns.map((weight) => `${weight}fr`)
    .join(" ");
}
