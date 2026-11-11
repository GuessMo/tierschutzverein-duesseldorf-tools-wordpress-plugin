import { createElement } from "@/services/factories";
import type { ElementKind } from "@/types/document";
import { elementMarkup } from "./elementMarkup";
import { findOverflows } from "./findOverflows";

function createProbe(artboard: HTMLElement): HTMLElement {
  const probe = artboard.cloneNode(true) as HTMLElement;
  probe.setAttribute("aria-hidden", "true");
  probe.style.position = "fixed";
  probe.style.left = "-100000px";
  probe.style.top = "0";
  probe.style.visibility = "hidden";
  probe.style.pointerEvents = "none";
  document.body.appendChild(probe);
  return probe;
}

function fitsIn(probe: HTMLElement, cellId: string, kind: ElementKind, known: string[]) {
  const cell = probe.querySelector<HTMLElement>(`.artboard-cell[data-overflow-id="${cellId}"]`);
  if (!cell) return false;
  const holder = document.createElement("div");
  holder.innerHTML = elementMarkup(createElement(kind));
  const node = holder.firstElementChild;
  if (!node) return false;
  cell.appendChild(node);
  const isNew = findOverflows(probe).some((id) => !known.includes(id));
  node.remove();
  return !isNew;
}

export function probeFits(artboard: HTMLElement, cellId: string, kinds: ElementKind[]) {
  const known = findOverflows(artboard);
  const probe = createProbe(artboard);
  try {
    return Object.fromEntries(kinds.map((kind) => [kind, fitsIn(probe, cellId, kind, known)]));
  } finally {
    probe.remove();
  }
}
