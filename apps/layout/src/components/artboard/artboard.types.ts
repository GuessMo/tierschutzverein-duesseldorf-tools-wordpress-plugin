import type { Cell, LayoutDocument, LayoutElement, Page, Section } from "@/types/document";

export interface ArtboardProps {
  document: LayoutDocument;
  page: Page;
  width: number | string;
  height: number | string;
  onBackgroundClick: () => void;
  isStatic?: boolean;
}

export interface SectionViewProps {
  section: Section;
}

export interface CellViewProps {
  section: Section;
  cell: Cell;
}

export interface ElementViewProps {
  section: Section;
  cell: Cell;
  element: LayoutElement;
}

export interface ElementRendererProps {
  element: LayoutElement;
}
