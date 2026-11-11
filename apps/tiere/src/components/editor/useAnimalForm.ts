import { useCallback, useMemo, useState, type FormEvent } from "react";
import { describeError } from "@web-app-kit/frontend";
import { toast } from "@web-app-kit/frontend/ui";
import { animalsApi } from "@/api";
import { animalHash } from "@/hooks";
import { draftToPayload, emptyDraft, recordToDraft } from "@/lib";
import type { AnimalDraft, AnimalRecord, DraftPatch } from "@/types";
import type { AnimalForm } from "./editor.types";

const SAVE_ERROR = "Speichern ist fehlgeschlagen. Bitte versuche es erneut.";

function initialDraft(record: AnimalRecord | null): AnimalDraft {
  return record ? recordToDraft(record) : emptyDraft();
}

function save(draft: AnimalDraft, record: AnimalRecord | null): Promise<AnimalRecord> {
  const payload = draftToPayload(draft, record?.status ?? "draft");
  return record ? animalsApi.update(record.id, payload) : animalsApi.create(payload);
}

export function useAnimalForm(initial: AnimalRecord | null): AnimalForm {
  const [record, setRecord] = useState(initial);
  const [draft, setDraft] = useState(() => initialDraft(initial));
  const [isBusy, setIsBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const patch = useCallback((next: DraftPatch) => setDraft((current) => ({ ...current, ...next })), []);
  const baseline = useMemo(() => JSON.stringify(initialDraft(record)), [record]);
  const isDirty = JSON.stringify(draft) !== baseline;

  const run = async (action: () => Promise<AnimalRecord>, message: string) => {
    setIsBusy(true);
    setError(null);
    try {
      const saved = await action();
      toast.success(message);
      if (!record) window.location.hash = animalHash(saved.id);
      setRecord(saved);
      setDraft(recordToDraft(saved));
    } catch (caught) {
      setError(describeError(caught, SAVE_ERROR));
    } finally {
      setIsBusy(false);
    }
  };

  const handleSubmit = (event: FormEvent) => {
    event.preventDefault();
    void run(() => save(draft, record), record ? "Änderungen gespeichert." : "Tier angelegt.");
  };

  const restore = () => {
    if (record) void run(() => animalsApi.restore(record.id), "Tier wiederhergestellt.");
  };

  return { draft, record, isBusy, isDirty, error, patch, handleSubmit, restore };
}
