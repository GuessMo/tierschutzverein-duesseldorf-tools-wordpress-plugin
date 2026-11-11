import { useState, type FormEvent } from "react";
import { useSubmitState } from "@/components/common";
import type { Workspace } from "@/hooks";
import { toDraft } from "@/lib/entryDraft";
import type { Entry } from "@/types";
import type { EntryDraft } from "@/types/draft";
import { useEntrySave } from "./useEntrySave";

export function useEntryForm(workspace: Workspace, entry: Entry | undefined, onClose: () => void) {
  const [draft, setDraft] = useState(() => toDraft(entry));
  const { isBusy, error, run } = useSubmitState();
  const save = useEntrySave(workspace, entry);
  const patch = (next: Partial<EntryDraft>) => setDraft((current) => ({ ...current, ...next }));
  const handleSubmit = (event: FormEvent) => {
    event.preventDefault();
    void run(() => save(draft), onClose);
  };
  return { draft, patch, isBusy, error, handleSubmit };
}
