import { useCallback, useEffect, useMemo, useState } from "react";
import { toast } from "@web-app-kit/frontend/ui";
import { newsletterApi } from "@/api";
import type { IssueDetail, NewsletterBlock } from "@/types";

interface Draft {
  title: string;
  blocks: NewsletterBlock[];
}

function snapshot(draft: Draft): string {
  return JSON.stringify(draft);
}

export function useIssueEditor(id: number) {
  const [issue, setIssue] = useState<IssueDetail | null>(null);
  const [draft, setDraft] = useState<Draft>({ title: "", blocks: [] });
  const [saved, setSaved] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);
  const apply = useCallback((detail: IssueDetail) => {
    const next = { title: detail.title, blocks: detail.blocks };
    setIssue(detail);
    setDraft(next);
    setSaved(snapshot(next));
  }, []);
  useEffect(() => {
    newsletterApi
      .get(id)
      .then(apply, () => setError("Der Newsletter konnte nicht geladen werden."));
  }, [id, apply]);
  const isDirty = useMemo(() => saved !== "" && snapshot(draft) !== saved, [draft, saved]);
  const save = useCallback(async () => {
    setIsSaving(true);
    try {
      apply(await newsletterApi.save(id, draft.title, draft.blocks));
      return true;
    } catch {
      toast.error("Speichern fehlgeschlagen. Bitte versuche es noch einmal.");
      return false;
    } finally {
      setIsSaving(false);
    }
  }, [id, draft, apply]);
  return { issue, setIssue, draft, setDraft, isDirty, isSaving, save, error };
}
