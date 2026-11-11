import { useLayoutEffect, useRef, type RefObject } from "react";
import { findOverflows } from "@/services/space";
import { revertLastEdit, useEditorStore } from "@/store/editorStore";
import { useSpaceStore } from "@/store/spaceStore";

const NOTICE =
  "Kein Platz mehr. Kürze den Text, wähle eine kleinere Größe oder nutze einen neuen Bereich.";

export function useSpaceGuard(artboardRef: RefObject<HTMLDivElement | null>, width: number): void {
  const document = useEditorStore((state) => state.document);
  const activePageId = useEditorStore((state) => state.activePageId);
  const lastAction = useEditorStore((state) => state.lastAction);
  const { setOverflowIds, setNotice } = useSpaceStore.getState();
  const baseline = useRef<string[]>([]);

  useLayoutEffect(() => {
    const artboard = artboardRef.current;
    if (!artboard || !document || width === 0) return;
    const overflows = findOverflows(artboard);
    const isNew = overflows.some((id) => !baseline.current.includes(id));
    if (lastAction === "edit" && isNew) {
      setNotice(NOTICE);
      revertLastEdit();
      return;
    }
    if (lastAction !== "history") setNotice(null);
    baseline.current = overflows;
    setOverflowIds(overflows);
  }, [artboardRef, document, activePageId, lastAction, width, setNotice, setOverflowIds]);
}
