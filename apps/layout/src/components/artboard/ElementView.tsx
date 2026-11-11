import type { PointerEvent } from "react";
import { isSelected } from "@/store/selectors";
import { useContrastStore } from "@/store/contrastStore";
import { useEditorStore } from "@/store/editorStore";
import type { ElementViewProps } from "./artboard.types";
import { ELEMENT_RENDERERS } from "./ElementRenderers";
import { elementStyle } from "./elementStyle";

export function ElementView({ section, cell, element }: ElementViewProps) {
  const selection = useEditorStore((state) => state.selection);
  const select = useEditorStore((state) => state.select);
  const Renderer = ELEMENT_RENDERERS[element.kind];
  const hasIssue = useContrastStore((state) =>
    state.issues.some((issue) => issue.elementId === element.id),
  );

  const handlePointerUp = (event: PointerEvent) => {
    event.stopPropagation();
    select({ type: "element", sectionId: section.id, cellId: cell.id, elementId: element.id });
  };

  return (
    <div
      className="artboard-element"
      data-kind={element.kind}
      data-section-id={section.id}
      data-cell-id={cell.id}
      data-element-id={element.id}
      data-contrast-issue={hasIssue || undefined}
      data-align={element.align}
      data-fills={element.kind === "image" && element.imageShape === "fill" ? "" : undefined}
      data-selected={isSelected(selection, "element", element.id) || undefined}
      style={elementStyle(element)}
      onPointerUp={handlePointerUp}
    >
      <Renderer element={element} />
    </div>
  );
}
