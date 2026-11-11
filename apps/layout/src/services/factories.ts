import { findElementDefinition } from "@/config/elements";
import { findSplit } from "@/config/splits";
import type { Cell, ElementKind, LayoutElement, Section, SplitId } from "@/types/document";
import { createId } from "@/utils/id";

export function createElement(
  kind: ElementKind,
  overrides: Partial<LayoutElement> = {},
): LayoutElement {
  return { ...findElementDefinition(kind).create(), ...overrides, id: createId() };
}

export function createCell(elements: LayoutElement[] = [], overrides: Partial<Cell> = {}): Cell {
  return { verticalAlign: "start", tone: "transparent", ...overrides, elements, id: createId() };
}

export function createSection(split: SplitId, overrides: Partial<Section> = {}): Section {
  const cells = findSplit(split).columns.map(() => createCell());
  return {
    tone: "white",
    padding: "m",
    gap: "m",
    height: "auto",
    overlay: false,
    cells,
    ...overrides,
    split,
    id: createId(),
  };
}

export function withCells(section: Section, cells: Cell[]): Section {
  return { ...section, cells };
}
