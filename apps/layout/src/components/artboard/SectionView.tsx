import type { CSSProperties, PointerEvent } from "react";
import { SPACING_SCALE } from "@/config/scales";
import { toGridColumns } from "@/config/splits";
import { findTone } from "@/config/tones";
import { isSelected } from "@/store/selectors";
import { useEditorStore } from "@/store/editorStore";
import { useSpaceStore } from "@/store/spaceStore";
import type { SectionViewProps } from "./artboard.types";
import { CellView } from "./CellView";

export function SectionView({ section }: SectionViewProps) {
  const selection = useEditorStore((state) => state.selection);
  const select = useEditorStore((state) => state.select);
  const overflowIds = useSpaceStore((state) => state.overflowIds);
  const tone = findTone(section.tone);
  const style = {
    "--pad": SPACING_SCALE[section.padding],
    "--gap": SPACING_SCALE[section.gap],
    gridTemplateColumns: toGridColumns(section.split),
    background: tone.background,
    color: tone.foreground,
  } as CSSProperties;

  const handlePointerUp = (event: PointerEvent) => {
    event.stopPropagation();
    select({ type: "section", sectionId: section.id });
  };

  return (
    <section
      className="artboard-section"
      data-height={section.height}
      data-overflow-id={section.id}
      data-overflow={overflowIds.includes(section.id) || undefined}
      data-inset={section.inset || undefined}
      data-selected={isSelected(selection, "section", section.id) || undefined}
      style={style}
      onPointerUp={handlePointerUp}
    >
      {section.backgroundImage && (
        <img className="artboard-section__image" src={section.backgroundImage} alt="" />
      )}
      {section.backgroundImage && section.overlay && <span className="artboard-section__overlay" />}
      {section.cells.map((cell) => (
        <CellView key={cell.id} section={section} cell={cell} />
      ))}
    </section>
  );
}
