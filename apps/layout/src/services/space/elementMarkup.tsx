import { renderToStaticMarkup } from "react-dom/server";
import { ELEMENT_RENDERERS } from "@/components/artboard/ElementRenderers";
import { elementStyle } from "@/components/artboard/elementStyle";
import type { LayoutElement } from "@/types/document";

export function elementMarkup(element: LayoutElement): string {
  const Renderer = ELEMENT_RENDERERS[element.kind];
  const fills = element.kind === "image" && element.imageShape === "fill" ? "" : undefined;
  return renderToStaticMarkup(
    <div
      className="artboard-element"
      data-kind={element.kind}
      data-align={element.align}
      data-fills={fills}
      style={elementStyle(element)}
    >
      <Renderer element={element} />
    </div>,
  );
}
