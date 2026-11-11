import type { EntryKind } from "@/types";

export const KIND_LABELS: Record<EntryKind, string> = {
  study: "Studie",
  law: "Gesetz/Urteil",
  statement: "Stellungnahme",
  press: "Presse",
  book: "Buch",
  other: "Sonstiges",
};

export const KIND_OPTIONS = Object.entries(KIND_LABELS) as [EntryKind, string][];
