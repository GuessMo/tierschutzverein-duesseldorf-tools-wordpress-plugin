import type { PostStatus, RecordType, SaveStatus, ValueLabel } from "@/types";

export const POST_STATUS_LABELS: Record<PostStatus, string> = {
  publish: "Veröffentlicht",
  future: "Geplant",
  draft: "Entwurf",
  pending: "Zur Prüfung",
  private: "Privat",
  trash: "Papierkorb",
};

export const SAVE_STATUSES: SaveStatus[] = ["draft", "pending", "publish"];

export const RECORD_TYPES: { value: RecordType; label: string }[] = [
  { value: "vermittlung", label: "Vermittlung" },
  { value: "vermisst", label: "Vermisst / gefunden" },
];

export const MEDIATORS: ValueLabel[] = [
  { value: "tierschutzverein", label: "Tierschutzverein" },
  { value: "privat", label: "Privat" },
];

export const CONTACT_FIELDS: { key: string; label: string; type: string; autoComplete: string }[] =
  [
    { key: "firstname", label: "Vorname", type: "text", autoComplete: "off" },
    { key: "lastname", label: "Nachname", type: "text", autoComplete: "off" },
    { key: "address", label: "Anschrift", type: "text", autoComplete: "off" },
    { key: "email", label: "E-Mail-Adresse", type: "email", autoComplete: "off" },
  ];

export const ALL = "all";
