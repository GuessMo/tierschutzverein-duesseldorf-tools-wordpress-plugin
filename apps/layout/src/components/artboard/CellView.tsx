import type { CSSProperties, PointerEvent } from "react";
import { findTone } from "@/config/tones";
import { isSelected } from "@/store/selectors";
import { useEditorStore } from "@/store/editorStore";
import { useSpaceStore } from "@/store/spaceStore";
import type { CellViewProps } from "./artboard.types";
import { ElementView } from "./ElementView";

export function CellView({ section, cell }: CellViewProps) {
  const selection = useEditorStore((state) => state.selection);
  const select = useEditorStore((state) => state.select);
  const overflowIds = useSpaceStore((state) => state.overflowIds);
  const tone = findTone(cell.tone);
  const style: CSSProperties =
    cell.tone === "transparent" ? {} : { background: tone.background, color: tone.foreground };

  const handlePointerUp = (event: PointerEvent) => {
    event.stopPropagation();
    select({ type: "cell", sectionId: section.id, cellId: cell.id });
  };

  return (
    <div
      className="artboard-cell"
      data-valign={cell.verticalAlign}
      data-overflow-id={cell.id}
      data-overflow={overflowIds.includes(cell.id) || undefined}
      data-toned={cell.tone !== "transparent" || undefined}
      data-empty={cell.elements.length === 0 || undefined}
      data-selected={isSelected(selection, "cell", cell.id) || undefined}
      style={style}
      onPointerUp={handlePointerUp}
    >
      {cell.elements.map((element) => (
        <ElementView key={element.id} section={section} cell={cell} element={element} />
      ))}
    </div>
  );
}
