import type {
  ElementKind,
  LayoutElement,
  OrientationId,
  Section,
  SplitId,
  ToneId,
} from "./document";

export type FormatGroup = "print" | "social" | "office";

export interface FormatDefinition {
  id: string;
  label: string;
  group: FormatGroup;
  widthMm?: number;
  heightMm?: number;
  widthPx: number;
  heightPx: number;
  isAvailable: boolean;
}

export interface ToneDefinition {
  id: ToneId;
  label: string;
  background: string;
  foreground: string;
  accent: string;
}

export interface SplitDefinition {
  id: SplitId;
  label: string;
  columns: number[];
}

export interface ElementDefinition {
  kind: ElementKind;
  label: string;
  create: () => Omit<LayoutElement, "id">;
}

export interface SectionPreset {
  id: string;
  label: string;
  create: () => Section;
}

export interface DocumentTemplate {
  id: string;
  label: string;
  formatId: string;
  orientation?: OrientationId;
  tone?: ToneId;
  createSections: () => Section[];
  createBackSections?: () => Section[];
}

export type Selection =
  | { type: "none" }
  | { type: "section"; sectionId: string }
  | { type: "cell"; sectionId: string; cellId: string }
  | { type: "element"; sectionId: string; cellId: string; elementId: string };

export interface PageDimensions {
  format: FormatDefinition;
  isPrint: boolean;
  widthPx: number;
  heightPx: number;
  widthMm: number;
  heightMm: number;
  bleedMm: number;
  outerRatio: number;
  bleedPercent: number;
}
