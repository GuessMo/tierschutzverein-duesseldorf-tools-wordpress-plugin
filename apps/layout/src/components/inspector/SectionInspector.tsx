import { OptionGroup, ToneSwatches } from "@/components/controls";
import { changeSplit, mapSection } from "@/services/sectionOperations";
import { useEditorStore } from "@/store/editorStore";
import type { Section } from "@/types/document";
import { CheckRow } from "./CheckRow";
import { SectionBackgroundFields } from "./SectionBackgroundFields";
import type { SectionInspectorProps } from "./inspector.types";
import { HEIGHT_OPTIONS, SPACING_OPTIONS, SPLIT_OPTIONS } from "./options";

export function SectionInspector({ section }: SectionInspectorProps) {
  const applyPage = useEditorStore((state) => state.applyPage);
  const update = (change: (current: Section) => Section) =>
    applyPage((document) => mapSection(document, section.id, change));
  const patch = (values: Partial<Section>) => update((current) => ({ ...current, ...values }));

  return (
    <div className="flex flex-col gap-5">
      <OptionGroup
        label="Raster"
        value={section.split}
        options={SPLIT_OPTIONS}
        onChange={(split) => update((current) => changeSplit(current, split))}
      />
      <ToneSwatches
        label="Hintergrund"
        value={section.tone}
        allowTransparent
        onChange={(tone) => patch({ tone })}
      />
      <SectionBackgroundFields section={section} />
      <CheckRow
        label="Als abgerundete Fläche mit Rand"
        checked={Boolean(section.inset)}
        onChange={(inset) => patch({ inset })}
      />
      <OptionGroup
        label="Höhe"
        value={section.height}
        options={HEIGHT_OPTIONS}
        onChange={(height) => patch({ height })}
      />
      <OptionGroup
        label="Innenabstand"
        value={section.padding}
        options={SPACING_OPTIONS}
        onChange={(padding) => patch({ padding })}
      />
      <OptionGroup
        label="Spaltenabstand"
        value={section.gap}
        options={SPACING_OPTIONS}
        onChange={(gap) => patch({ gap })}
      />
    </div>
  );
}
