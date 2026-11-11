import { useEffect, type RefObject } from "react";
import { ELEMENT_DEFINITIONS } from "@/config/elements";
import { probeFits } from "@/services/space";
import { selectActivePage, useEditorStore } from "@/store/editorStore";
import { targetCell } from "@/store/selectors";
import { useSpaceStore } from "@/store/spaceStore";

const KINDS = ELEMENT_DEFINITIONS.map((definition) => definition.kind);
const PROBE_DELAY_MS = 150;

export function useElementFit(artboardRef: RefObject<HTMLDivElement | null>, width: number): void {
  const page = useEditorStore(selectActivePage);
  const selection = useEditorStore((state) => state.selection);
  const setPlaceable = useSpaceStore((state) => state.setPlaceable);

  useEffect(() => {
    const artboard = artboardRef.current;
    const address = page ? targetCell(page, selection) : null;
    if (!artboard || !address || width === 0) {
      setPlaceable({});
      return;
    }
    const timer = window.setTimeout(
      () => setPlaceable(probeFits(artboard, address.cellId, KINDS)),
      PROBE_DELAY_MS,
    );
    return () => window.clearTimeout(timer);
  }, [artboardRef, page, selection, width, setPlaceable]);
}
