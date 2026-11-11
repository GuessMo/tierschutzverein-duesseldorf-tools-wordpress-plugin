import { useEffect } from "react";
import { useEditorStore } from "@/store/editorStore";
import { useLibraryStore } from "@/store/libraryStore";

const AUTOSAVE_DELAY_MS = 400;

export function useAutosave(): void {
  const document = useEditorStore((state) => state.document);
  const persist = useLibraryStore((state) => state.persist);

  useEffect(() => {
    if (!document) return;
    const timer = window.setTimeout(() => void persist(document), AUTOSAVE_DELAY_MS);
    return () => window.clearTimeout(timer);
  }, [document, persist]);
}
