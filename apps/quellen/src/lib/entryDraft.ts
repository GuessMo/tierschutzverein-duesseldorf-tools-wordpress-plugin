import type { Entry, EntryInput } from "@/types";
import type { EntryDraft } from "@/types/draft";

export function toDraft(entry?: Entry): EntryDraft {
  return {
    title: entry?.title ?? "",
    body: entry?.body ?? "",
    url: entry?.url ?? "",
    publisher: entry?.publisher ?? "",
    year: entry?.year ? String(entry.year) : "",
    kind: entry?.kind ?? "other",
    accessed_at: entry?.accessed_at ?? "",
    term_ids: entry?.term_ids ?? [],
  };
}

export function toInput(draft: EntryDraft): EntryInput {
  const year = Number.parseInt(draft.year, 10);
  return {
    ...draft,
    title: draft.title.trim(),
    url: draft.url.trim(),
    year: Number.isNaN(year) ? null : year,
    accessed_at: draft.accessed_at || null,
  };
}
