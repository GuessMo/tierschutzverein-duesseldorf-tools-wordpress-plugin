import { mapSection } from "@/services/sectionOperations";
import { useEditorStore } from "@/store/editorStore";
import type { Section } from "@/types/document";
import { CheckRow } from "./CheckRow";
import { ImageUpload } from "./ImageUpload";
import type { SectionInspectorProps } from "./inspector.types";

export function SectionBackgroundFields({ section }: SectionInspectorProps) {
  const applyPage = useEditorStore((state) => state.applyPage);
  const patch = (values: Partial<Section>) =>
    applyPage((document) =>
      mapSection(document, section.id, (current) => ({ ...current, ...values })),
    );

  return (
    <>
      <ImageUpload
        label="Hintergrundbild des Bereichs"
        value={section.backgroundImage}
        onChange={(backgroundImage) => patch({ backgroundImage })}
      />
      {section.backgroundImage && (
        <CheckRow
          label="Abdunkeln für lesbaren Text"
          checked={section.overlay}
          onChange={(overlay) => patch({ overlay })}
        />
      )}
    </>
  );
}
