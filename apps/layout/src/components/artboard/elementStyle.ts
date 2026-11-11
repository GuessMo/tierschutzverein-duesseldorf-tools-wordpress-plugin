import type { CSSProperties } from "react";
import { ELEMENT_SCALES } from "@/config/scales";
import { findTone } from "@/config/tones";
import type { LayoutElement } from "@/types/document";

export function elementStyle(element: LayoutElement): CSSProperties {
  const tone = findTone(element.tone);
  return {
    "--size": ELEMENT_SCALES[element.kind][element.size],
    "--tone-bg": tone.background,
    "--tone-fg": tone.foreground,
  } as CSSProperties;
}

export function textColor(element: LayoutElement): CSSProperties {
  if (element.tone === "transparent") return {};
  return { color: findTone(element.tone).background };
}
