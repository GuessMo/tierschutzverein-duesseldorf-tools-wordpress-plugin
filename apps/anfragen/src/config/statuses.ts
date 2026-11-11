import type { CountKey, ListStatus } from "@/types";

export const PRIMARY_STATUSES: { value: ListStatus; label: string }[] = [
  { value: "open", label: "Offen" },
  { value: "answered", label: "Beantwortet" },
  { value: "", label: "Alle" },
];

export const MORE_STATUSES: { value: ListStatus; label: string }[] = [
  { value: "spam", label: "Spam" },
  { value: "blocked", label: "Blockiert" },
  { value: "trash", label: "Papierkorb" },
];

export const STATUS_LABELS: Record<string, string> = {
  open: "Offen",
  answered: "Beantwortet",
  spam: "Spam",
  blocked: "Blockiert",
};

export function countKey(status: ListStatus): CountKey {
  return status === "" ? "all" : status;
}

export function isMoreStatus(status: ListStatus): boolean {
  return MORE_STATUSES.some((item) => item.value === status);
}

export function moreStatusLabel(status: ListStatus): string {
  return MORE_STATUSES.find((item) => item.value === status)?.label ?? "";
}
