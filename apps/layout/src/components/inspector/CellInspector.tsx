import { OptionGroup, ToneSwatches } from "@/components/controls";
import { mapCell } from "@/services/elementOperations";
import { useEditorStore } from "@/store/editorStore";
import type { Cell } from "@/types/document";
import type { CellInspectorProps } from "./inspector.types";
import { SectionBackgroundFields } from "./SectionBackgroundFields";
import { VERTICAL_OPTIONS } from "./options";

export function CellInspector({ section, cell }: CellInspectorProps) {
  const applyPage = useEditorStore((state) => state.applyPage);
  const address = { sectionId: section.id, cellId: cell.id };
  const patch = (values: Partial<Cell>) =>
    applyPage((document) => mapCell(document, address, (current) => ({ ...current, ...values })));

  return (
    <div className="flex flex-col gap-5">
      <OptionGroup
        label="Ausrichtung vertikal"
        value={cell.verticalAlign}
        options={VERTICAL_OPTIONS}
        onChange={(verticalAlign) => patch({ verticalAlign })}
      />
      <ToneSwatches label="Fläche" value={cell.tone} onChange={(tone) => patch({ tone })} />
      {section.backgroundImage && <SectionBackgroundFields section={section} />}
      <p className="text-xs text-muted-foreground">
        Elemente fügst du links unter „Elemente“ in diese Spalte ein.
      </p>
    </div>
  );
}
