import { SIZE_LABELS, SPACING_LABELS } from "@/config/scales";
import { SPLITS } from "@/config/splits";
import type { OptionItem } from "@/components/controls/controls.types";
import type {
  AlignId,
  ImageShapeId,
  SectionHeightId,
  SizeId,
  SpacingId,
  SplitId,
} from "@/types/document";

export const SPLIT_OPTIONS: OptionItem<SplitId>[] = SPLITS.map((split) => ({
  value: split.id,
  label: split.label,
}));

export const SIZE_OPTIONS: OptionItem<SizeId>[] = (Object.keys(SIZE_LABELS) as SizeId[]).map(
  (value) => ({
    value,
    label: SIZE_LABELS[value],
  }),
);

export const SPACING_OPTIONS: OptionItem<SpacingId>[] = (
  Object.keys(SPACING_LABELS) as SpacingId[]
).map((value) => ({ value, label: SPACING_LABELS[value] }));

export const ALIGN_OPTIONS: OptionItem<AlignId>[] = [
  { value: "start", label: "Links" },
  { value: "center", label: "Mitte" },
  { value: "end", label: "Rechts" },
];

export const VERTICAL_OPTIONS: OptionItem<AlignId>[] = [
  { value: "start", label: "Oben" },
  { value: "center", label: "Mitte" },
  { value: "end", label: "Unten" },
];

export const HEIGHT_OPTIONS: OptionItem<SectionHeightId>[] = [
  { value: "auto", label: "Nach Inhalt" },
  { value: "fill", label: "Rest füllen" },
];

export const IMAGE_SHAPE_OPTIONS: OptionItem<ImageShapeId>[] = [
  { value: "fill", label: "Füllen" },
  { value: "landscape", label: "4:3" },
  { value: "portrait", label: "3:4" },
  { value: "square", label: "1:1" },
  { value: "circle", label: "Kreis" },
  { value: "polaroid", label: "Polaroid" },
];
