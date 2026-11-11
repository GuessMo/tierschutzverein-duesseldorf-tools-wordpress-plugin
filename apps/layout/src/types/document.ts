export type ToneId =
  | "white"
  | "beige"
  | "sand"
  | "green"
  | "greenDark"
  | "brown"
  | "pink"
  | "transparent";

export type SizeId = "s" | "m" | "l" | "xl";

export type AlignId = "start" | "center" | "end";

export type SplitId = "full" | "half" | "thirds" | "oneTwo" | "twoOne" | "quarters";

export type SpacingId = "none" | "s" | "m" | "l";

export type SectionHeightId = "auto" | "fill";

export type ImageShapeId = "fill" | "square" | "landscape" | "portrait" | "circle" | "polaroid";

export type ElementKind =
  | "headline"
  | "text"
  | "image"
  | "logo"
  | "button"
  | "contact"
  | "badge"
  | "divider"
  | "spacer";

export interface LayoutElement {
  id: string;
  kind: ElementKind;
  text: string;
  size: SizeId;
  tone: ToneId;
  align: AlignId;
  imageSrc?: string;
  imageShape?: ImageShapeId;
}

export interface Cell {
  id: string;
  verticalAlign: AlignId;
  tone: ToneId;
  elements: LayoutElement[];
}

export interface Section {
  id: string;
  split: SplitId;
  tone: ToneId;
  padding: SpacingId;
  gap: SpacingId;
  height: SectionHeightId;
  backgroundImage?: string;
  overlay: boolean;
  inset?: boolean;
  cells: Cell[];
}

export type OrientationId = "portrait" | "landscape";

export interface SectionsHolder {
  sections: Section[];
}

export interface Page extends SectionsHolder {
  id: string;
}

export interface LayoutDocument {
  id: string;
  name: string;
  formatId: string;
  orientation?: OrientationId;
  tone?: ToneId;
  pages: Page[];
  updatedAt: string;
}
