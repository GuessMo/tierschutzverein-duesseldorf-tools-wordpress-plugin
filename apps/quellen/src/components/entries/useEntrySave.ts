import { entriesApi } from "@/api";
import type { Workspace } from "@/hooks";
import { toInput } from "@/lib/entryDraft";
import type { Entry } from "@/types";
import type { EntryDraft } from "@/types/draft";

export function useEntrySave(workspace: Workspace, entry?: Entry) {
  const { collection, entries } = workspace;
  return async (draft: EntryDraft) => {
    const input = toInput(draft);
    if (entry) await entriesApi.update(entry.id, input);
    else await entriesApi.create(collection.data?.id ?? 0, input);
    await Promise.all([entries.reload(), collection.reload()]);
  };
}
