import type { Cell, LayoutElement, Section } from "@/types/document";

export interface SectionInspectorProps {
  section: Section;
}

export interface CellInspectorProps {
  section: Section;
  cell: Cell;
}

export interface ElementInspectorProps {
  section: Section;
  cell: Cell;
  element: LayoutElement;
}

export interface ImageUploadProps {
  label: string;
  value?: string;
  onChange: (value: string | undefined) => void;
}

export interface CheckRowProps {
  label: string;
  checked: boolean;
  onChange: (checked: boolean) => void;
}
