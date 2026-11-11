import type { EntryKind } from "./sources";

export interface EntryDraft {
  title: string;
  body: string;
  url: string;
  publisher: string;
  year: string;
  kind: EntryKind;
  accessed_at: string;
  term_ids: number[];
}
