import { Input, Textarea } from "@web-app-kit/frontend/ui";
import { useId } from "react";
import { FieldRow, OptionGroup, ToneSwatches } from "@/components/controls";
import { hasEditableText } from "@/config/elements";
import { updateElement } from "@/services/elementOperations";
import { useEditorStore } from "@/store/editorStore";
import type { LayoutElement } from "@/types/document";
import { ContrastAlert } from "./ContrastAlert";
import { ElementActions } from "./ElementActions";
import { ImageUpload } from "./ImageUpload";
import type { ElementInspectorProps } from "./inspector.types";
import { ALIGN_OPTIONS, IMAGE_SHAPE_OPTIONS, SIZE_OPTIONS } from "./options";

const MULTILINE_KINDS = new Set(["text", "contact", "headline"]);
const SOLID_KINDS = new Set(["button", "badge"]);
const TONE_LABELS: Partial<Record<LayoutElement["kind"], string>> = {
  button: "Button-Farbe",
  badge: "Störer-Farbe",
  image: "Platzhalter",
};

export function ElementInspector(props: ElementInspectorProps) {
  const { section, cell, element } = props;
  const textId = useId();
  const applyPage = useEditorStore((state) => state.applyPage);
  const patch = (values: Partial<LayoutElement>) =>
    applyPage((document) =>
      updateElement(document, { sectionId: section.id, cellId: cell.id }, element.id, values),
    );
  const isImage = element.kind === "image";
  const TextControl = MULTILINE_KINDS.has(element.kind) ? Textarea : Input;

  return (
    <div className="flex flex-col gap-5">
      <ContrastAlert elementId={element.id} />
      {hasEditableText(element) && (
        <FieldRow label="Text" htmlFor={textId}>
          <TextControl
            id={textId}
            value={element.text}
            onChange={(event) => patch({ text: event.target.value })}
          />
        </FieldRow>
      )}
      {isImage && (
        <ImageUpload
          label="Bild"
          value={element.imageSrc}
          onChange={(imageSrc) => patch({ imageSrc })}
        />
      )}
      {isImage && (
        <OptionGroup
          label="Form"
          value={element.imageShape ?? "landscape"}
          options={IMAGE_SHAPE_OPTIONS}
          onChange={(imageShape) => patch({ imageShape })}
        />
      )}
      {!isImage && (
        <OptionGroup
          label="Größe"
          value={element.size}
          options={SIZE_OPTIONS}
          onChange={(size) => patch({ size })}
        />
      )}
      <OptionGroup
        label="Ausrichtung"
        value={element.align}
        options={ALIGN_OPTIONS}
        onChange={(align) => patch({ align })}
      />
      {element.kind !== "spacer" && (
        <ToneSwatches
          label={TONE_LABELS[element.kind] ?? "Farbe"}
          value={element.tone}
          allowTransparent={!SOLID_KINDS.has(element.kind)}
          onChange={(tone) => patch({ tone })}
        />
      )}
      <ElementActions {...props} />
    </div>
  );
}
