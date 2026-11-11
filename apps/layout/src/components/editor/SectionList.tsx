import { Button } from "@web-app-kit/frontend/ui";
import { IconArrowDown, IconArrowUp, IconCopy, IconPhoto, IconTrash } from "@tabler/icons-react";
import { PanelSection } from "@/components/controls";
import { findSplit } from "@/config/splits";
import { findTone } from "@/config/tones";
import { cn } from "@/lib/utils";
import { duplicateSection, moveSection, removeSection } from "@/services/sectionOperations";
import { selectActivePage, useEditorStore } from "@/store/editorStore";
import { SectionChildren } from "./SectionChildren";
import { SplitGlyph } from "./SplitGlyph";

export function SectionList() {
  const sections = useEditorStore((state) => selectActivePage(state)?.sections ?? []);
  const selection = useEditorStore((state) => state.selection);
  const { applyPage, select } = useEditorStore.getState();
  const activeId = selection.type === "none" ? null : selection.sectionId;

  return (
    <PanelSection title="Bereiche">
      {sections.length === 0 && (
        <p className="text-xs text-muted-foreground">
          Noch keine Bereiche. Füge unten einen hinzu.
        </p>
      )}
      <ol className="flex flex-col gap-1">
        {sections.map((section, index) => (
          <li key={section.id} className="flex flex-col gap-0.5">
            <div
              className={cn(
                "group flex items-center rounded-md hover:bg-surface-hover",
                section.id === activeId && "bg-surface-selected",
              )}
            >
              <button
                type="button"
                className={cn(
                  "flex min-h-8 min-w-0 flex-1 items-center gap-2 rounded-md",
                  "px-2 text-left text-sm outline-none focus-visible:ring-3",
                  "focus-visible:ring-ring/50 hover:underline active:bg-surface-selected",
                )}
                onClick={() => select({ type: "section", sectionId: section.id })}
              >
                <span
                  className="size-3 shrink-0 rounded-full border"
                  style={{ background: findTone(section.tone).background }}
                />
                <SplitGlyph split={section.split} />
                <span className="truncate">{findSplit(section.split).label}</span>
                {section.backgroundImage && (
                  <IconPhoto
                    className="size-3.5 shrink-0 text-muted-foreground"
                    aria-label="mit Hintergrundbild"
                  />
                )}
              </button>
              <span className="flex opacity-60 group-hover:opacity-100 focus-within:opacity-100">
                <Button
                  variant="ghost"
                  size="icon-xs"
                  aria-label="Nach oben"
                  disabled={index === 0}
                  onClick={() => applyPage((document) => moveSection(document, section.id, -1))}
                >
                  <IconArrowUp />
                </Button>
                <Button
                  variant="ghost"
                  size="icon-xs"
                  aria-label="Nach unten"
                  disabled={index === sections.length - 1}
                  onClick={() => applyPage((document) => moveSection(document, section.id, 1))}
                >
                  <IconArrowDown />
                </Button>
                <Button
                  variant="ghost"
                  size="icon-xs"
                  aria-label="Duplizieren"
                  onClick={() => applyPage((document) => duplicateSection(document, section.id))}
                >
                  <IconCopy />
                </Button>
                <Button
                  variant="ghost"
                  size="icon-xs"
                  aria-label="Löschen"
                  onClick={() => applyPage((document) => removeSection(document, section.id))}
                >
                  <IconTrash />
                </Button>
              </span>
            </div>
            {section.id === activeId && <SectionChildren section={section} />}
          </li>
        ))}
      </ol>
    </PanelSection>
  );
}
