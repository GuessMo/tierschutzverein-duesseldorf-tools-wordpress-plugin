import { findElementDefinition } from "@/config/elements";
import { ELEMENT_ICONS } from "@/config/elementIcons";
import { cn } from "@/lib/utils";
import { isSelected } from "@/store/selectors";
import { useEditorStore } from "@/store/editorStore";
import type { Section } from "@/types/document";

const ITEM_CLASSES = [
  "flex min-h-7 w-full min-w-0 items-center gap-2 rounded-md px-2 text-left text-xs",
  "outline-none hover:bg-surface-hover active:bg-surface-selected",
  "focus-visible:ring-3 focus-visible:ring-ring/50",
].join(" ");

const SELECTED_CLASSES = "bg-surface-selected font-medium";

export function SectionChildren({ section }: { section: Section }) {
  const selection = useEditorStore((state) => state.selection);
  const select = useEditorStore((state) => state.select);
  return (
    <ul aria-label="Spalten und Elemente" className="ml-5 flex flex-col gap-0.5 border-l pl-2">
      {section.cells.map((cell, index) => (
        <li key={cell.id} className="flex flex-col gap-0.5">
          <button
            type="button"
            className={cn(ITEM_CLASSES, isSelected(selection, "cell", cell.id) && SELECTED_CLASSES)}
            onClick={() => select({ type: "cell", sectionId: section.id, cellId: cell.id })}
          >
            Spalte {index + 1}
          </button>
          <ul className="ml-3 flex flex-col gap-0.5">
            {cell.elements.map((element) => {
              const Icon = ELEMENT_ICONS[element.kind];
              const address = { sectionId: section.id, cellId: cell.id, elementId: element.id };
              const isActive = isSelected(selection, "element", element.id);
              return (
                <li key={element.id}>
                  <button
                    type="button"
                    className={cn(ITEM_CLASSES, isActive && SELECTED_CLASSES)}
                    onClick={() => select({ type: "element", ...address })}
                  >
                    <Icon aria-hidden className="size-3.5 shrink-0" />
                    <span className="truncate">{findElementDefinition(element.kind).label}</span>
                  </button>
                </li>
              );
            })}
          </ul>
        </li>
      ))}
    </ul>
  );
}
