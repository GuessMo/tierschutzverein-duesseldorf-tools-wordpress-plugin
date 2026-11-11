import { findSplit } from "@/config/splits";
import { createCell } from "@/services/factories";
import type { Cell, Section, SectionsHolder, SplitId } from "@/types/document";
import { insertAfter, moveItem } from "@/utils/arrays";
import { createId } from "@/utils/id";

export function mapSection<T extends SectionsHolder>(
  document: T,
  sectionId: string,
  update: (section: Section) => Section,
): T {
  return {
    ...document,
    sections: document.sections.map((section) =>
      section.id === sectionId ? update(section) : section,
    ),
  };
}

function resizeCells(cells: Cell[], count: number): Cell[] {
  if (cells.length === count) return cells;
  if (cells.length < count) {
    return [...cells, ...Array.from({ length: count - cells.length }, () => createCell())];
  }
  const kept = cells.slice(0, count);
  const overflow = cells.slice(count).flatMap((cell) => cell.elements);
  const last = kept[count - 1];
  return [...kept.slice(0, -1), { ...last, elements: [...last.elements, ...overflow] }];
}

export function changeSplit(section: Section, split: SplitId): Section {
  return { ...section, split, cells: resizeCells(section.cells, findSplit(split).columns.length) };
}

export function addSection<T extends SectionsHolder>(
  document: T,
  section: Section,
  afterSectionId?: string,
): T {
  const index = document.sections.findIndex((entry) => entry.id === afterSectionId);
  const position = index === -1 ? document.sections.length - 1 : index;
  return { ...document, sections: insertAfter(document.sections, position, section) };
}

export function moveSection<T extends SectionsHolder>(
  document: T,
  sectionId: string,
  delta: number,
): T {
  const index = document.sections.findIndex((entry) => entry.id === sectionId);
  return { ...document, sections: moveItem(document.sections, index, delta) };
}

export function removeSection<T extends SectionsHolder>(document: T, sectionId: string): T {
  return { ...document, sections: document.sections.filter((entry) => entry.id !== sectionId) };
}

export function cloneSection(section: Section): Section {
  return {
    ...section,
    id: createId(),
    cells: section.cells.map((cell) => ({
      ...cell,
      id: createId(),
      elements: cell.elements.map((element) => ({ ...element, id: createId() })),
    })),
  };
}

export function duplicateSection<T extends SectionsHolder>(document: T, sectionId: string): T {
  const original = document.sections.find((entry) => entry.id === sectionId);
  if (!original) return document;
  return addSection(document, cloneSection(original), sectionId);
}
