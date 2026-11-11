import { SidePanel } from "@web-app-kit/frontend/ui";
import { cn } from "@/lib/utils";
import { IconChevronRight } from "@tabler/icons-react";
import { findElementDefinition } from "@/config/elements";
import { resolveSelection } from "@/store/selectors";
import { selectActivePage, useEditorStore } from "@/store/editorStore";
import { CellInspector } from "./CellInspector";
import { DocumentInspector } from "./DocumentInspector";
import { ElementInspector } from "./ElementInspector";
import { SectionInspector } from "./SectionInspector";
import { SpaceNotice } from "./SpaceNotice";

function Breadcrumb() {
  const document = useEditorStore(selectActivePage);
  const selection = useEditorStore((state) => state.selection);
  const select = useEditorStore((state) => state.select);
  if (!document || selection.type === "none") return null;
  const { section, cell, element } = resolveSelection(document, selection);
  if (!section) return null;
  const cellIndex = cell ? section.cells.indexOf(cell) + 1 : 0;

  return (
    <nav className="flex flex-wrap items-center gap-1 text-xs" aria-label="Auswahl">
      <button
        type="button"
        className={cn(
          "rounded-sm px-1 py-0.5 text-muted-foreground hover:bg-surface-hover",
          "hover:text-foreground focus-visible:ring-3",
          "focus-visible:ring-ring/50 focus-visible:outline-none",
          "active:bg-accent",
        )}
        onClick={() => select({ type: "section", sectionId: section.id })}
      >
        Bereich {document.sections.indexOf(section) + 1}
      </button>
      {cell && <IconChevronRight className="size-3.5 text-muted-foreground" aria-hidden="true" />}
      {cell && (
        <button
          type="button"
          className={cn(
            "rounded-sm px-1 py-0.5 text-muted-foreground hover:bg-surface-hover",
            "hover:text-foreground focus-visible:ring-3",
            "focus-visible:ring-ring/50 focus-visible:outline-none",
            "active:bg-accent",
          )}
          onClick={() => select({ type: "cell", sectionId: section.id, cellId: cell.id })}
        >
          Spalte {cellIndex}
        </button>
      )}
      {element && (
        <IconChevronRight className="size-3.5 text-muted-foreground" aria-hidden="true" />
      )}
      {element && (
        <span className="px-1 font-medium">{findElementDefinition(element.kind).label}</span>
      )}
    </nav>
  );
}

function InspectorBody() {
  const document = useEditorStore(selectActivePage);
  const selection = useEditorStore((state) => state.selection);
  if (!document) return null;
  const { section, cell, element } = resolveSelection(document, selection);
  if (section && cell && element)
    return <ElementInspector section={section} cell={cell} element={element} />;
  if (section && cell) return <CellInspector section={section} cell={cell} />;
  if (section) return <SectionInspector section={section} />;
  return <DocumentInspector />;
}

export function Inspector() {
  return (
    <SidePanel side="right" width="md" title="Eigenschaften">
      <div className="flex flex-col gap-4 p-2">
        <SpaceNotice />
        <Breadcrumb />
        <InspectorBody />
      </div>
    </SidePanel>
  );
}
