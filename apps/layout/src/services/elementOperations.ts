import { mapSection } from "@/services/sectionOperations";
import type { Cell, LayoutElement, SectionsHolder } from "@/types/document";
import { insertAfter, moveItem } from "@/utils/arrays";
import { createId } from "@/utils/id";

export interface CellAddress {
  sectionId: string;
  cellId: string;
}

export function mapCell<T extends SectionsHolder>(
  document: T,
  address: CellAddress,
  update: (cell: Cell) => Cell,
): T {
  return mapSection(document, address.sectionId, (section) => ({
    ...section,
    cells: section.cells.map((cell) => (cell.id === address.cellId ? update(cell) : cell)),
  }));
}

function mapElements<T extends SectionsHolder>(
  document: T,
  address: CellAddress,
  update: (elements: LayoutElement[]) => LayoutElement[],
): T {
  return mapCell(document, address, (cell) => ({ ...cell, elements: update(cell.elements) }));
}

export function addElement<T extends SectionsHolder>(
  document: T,
  address: CellAddress,
  element: LayoutElement,
) {
  return mapElements(document, address, (elements) => [...elements, element]);
}

export function updateElement<T extends SectionsHolder>(
  document: T,
  address: CellAddress,
  elementId: string,
  patch: Partial<LayoutElement>,
): T {
  return mapElements(document, address, (elements) =>
    elements.map((element) => (element.id === elementId ? { ...element, ...patch } : element)),
  );
}

export function moveElement<T extends SectionsHolder>(
  document: T,
  address: CellAddress,
  elementId: string,
  delta: number,
) {
  return mapElements(document, address, (elements) =>
    moveItem(
      elements,
      elements.findIndex((element) => element.id === elementId),
      delta,
    ),
  );
}

export function removeElement<T extends SectionsHolder>(
  document: T,
  address: CellAddress,
  elementId: string,
) {
  return mapElements(document, address, (elements) =>
    elements.filter((element) => element.id !== elementId),
  );
}

export function duplicateElement<T extends SectionsHolder>(
  document: T,
  address: CellAddress,
  elementId: string,
) {
  return mapElements(document, address, (elements) => {
    const index = elements.findIndex((element) => element.id === elementId);
    if (index === -1) return elements;
    return insertAfter(elements, index, { ...elements[index], id: createId() });
  });
}
