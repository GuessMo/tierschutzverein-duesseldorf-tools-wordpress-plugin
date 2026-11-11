import { PanelSection } from "@/components/controls";
import { SECTION_PRESETS } from "@/config/sectionPresets";
import { SPLITS } from "@/config/splits";
import { createSection } from "@/services/factories";
import { addSection } from "@/services/sectionOperations";
import { useEditorStore } from "@/store/editorStore";
import type { Section } from "@/types/document";
import { SplitGlyph } from "./SplitGlyph";
import { TileButton } from "./TileButton";

function insertSection(section: Section): void {
  const { applyPage, select, selection } = useEditorStore.getState();
  const afterId = selection.type === "none" ? undefined : selection.sectionId;
  applyPage((document) => addSection(document, section, afterId));
  select({ type: "section", sectionId: section.id });
}

export function AddSectionPanel() {
  return (
    <PanelSection title="Bereich hinzufügen">
      <div className="grid grid-cols-3 gap-2">
        {SPLITS.map((split) => (
          <TileButton key={split.id} onClick={() => insertSection(createSection(split.id))}>
            <SplitGlyph split={split.id} />
            {split.label}
          </TileButton>
        ))}
      </div>
      <h3 className="pt-1 text-xs font-medium text-muted-foreground">Vorgefertigt</h3>
      <div className="grid grid-cols-2 gap-2">
        {SECTION_PRESETS.map((preset) => (
          <TileButton
            key={preset.id}
            className="min-h-10"
            onClick={() => insertSection(preset.create())}
          >
            {preset.label}
          </TileButton>
        ))}
      </div>
    </PanelSection>
  );
}
