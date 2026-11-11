import type { Animal } from "./conversation";

export type RelayedKind = "" | "listing" | "sighting";

export type RelayedStateLevel = "success" | "warning" | "danger";

export interface RelayedItem {
  id: number;
  createdLabel: string;
  kind: Exclude<RelayedKind, "">;
  kindLabel: string;
  name: string;
  email: string;
  phone: string;
  message: string;
  stateLevel: RelayedStateLevel;
  stateLabel: string;
  animal: Animal | null;
}

export interface RelayedMonth {
  value: string;
  label: string;
}

export interface RelayedResponse {
  items: RelayedItem[];
  total: number;
  page: number;
  pages: number;
  months: RelayedMonth[];
  retentionDays: number;
}

export interface RelayedFilter {
  kind: RelayedKind;
  search: string;
  month: string;
  page: number;
}
