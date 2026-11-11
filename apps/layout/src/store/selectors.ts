import type { Selection } from "@/types/catalog";
import type { Cell, LayoutElement, Section, SectionsHolder } from "@/types/document";

export interface ResolvedSelection {
  section?: Section;
  cell?: Cell;
  element?: LayoutElement;
}

export function resolveSelection(
  document: SectionsHolder,
  selection: Selection,
): ResolvedSelection {
  if (selection.type === "none") return {};
  const section = document.sections.find((entry) => entry.id === selection.sectionId);
  if (!section || selection.type === "section") return { section };
  const cell = section.cells.find((entry) => entry.id === selection.cellId);
  if (!cell || selection.type === "cell") return { section, cell };
  return {
    section,
    cell,
    element: cell.elements.find((entry) => entry.id === selection.elementId),
  };
}

export function isSelected(selection: Selection, type: Selection["type"], id: string): boolean {
  if (selection.type !== type) return false;
  if (selection.type === "section") return selection.sectionId === id;
  if (selection.type === "cell") return selection.cellId === id;
  if (selection.type === "element") return selection.elementId === id;
  return false;
}

export function targetCell(document: SectionsHolder, selection: Selection) {
  const resolved = resolveSelection(document, selection);
  const section = resolved.section ?? document.sections[document.sections.length - 1];
  const cell = resolved.cell ?? section?.cells[0];
  if (!section || !cell) return null;
  return { sectionId: section.id, cellId: cell.id };
}
