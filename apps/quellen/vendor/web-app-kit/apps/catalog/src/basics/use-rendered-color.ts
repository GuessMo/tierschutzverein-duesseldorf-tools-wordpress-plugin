import { useLayoutEffect, useRef, useState } from "react";
import { contrastRatio } from "@web-app-kit/frontend";
import type { RenderedColor, RoleKind } from "./basics.types";

function safeContrast(first: string, second: string): number | null {
  try {
    return contrastRatio(first, second);
  } catch {
    return null;
  }
}

function readColor(element: HTMLElement, kind: RoleKind): RenderedColor {
  const style = getComputedStyle(element);
  const background = style.backgroundColor;
  const foreground = kind === "line" ? style.borderTopColor : style.color;
  const value = kind === "line" ? foreground : background;
  return { value, ratio: safeContrast(foreground, background) };
}

export function useRenderedColor<T extends HTMLElement>(kind: RoleKind) {
  const ref = useRef<T>(null);
  const [color, setColor] = useState<RenderedColor | null>(null);
  useLayoutEffect(() => {
    if (ref.current) setColor(readColor(ref.current, kind));
  }, [kind]);
  return { ref, color };
}
