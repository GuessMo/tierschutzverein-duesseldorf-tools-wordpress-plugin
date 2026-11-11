import type { ElementKind, SizeId, SpacingId } from "@/types/document";

type SizeScale = Record<SizeId, number>;

const TEXT_SCALE: SizeScale = { s: 2.4, m: 3, l: 3.8, xl: 4.6 };

export const ELEMENT_SCALES: Record<ElementKind, SizeScale> = {
  headline: { s: 4.5, m: 6, l: 8, xl: 10.5 },
  text: TEXT_SCALE,
  contact: TEXT_SCALE,
  button: { s: 2.6, m: 3.2, l: 4, xl: 5 },
  badge: { s: 3, m: 4, l: 5.5, xl: 7 },
  logo: { s: 10, m: 16, l: 24, xl: 34 },
  spacer: { s: 2, m: 4, l: 8, xl: 14 },
  divider: { s: 0.2, m: 0.35, l: 0.6, xl: 1 },
  image: { s: 1, m: 1, l: 1, xl: 1 },
};

export const SPACING_SCALE: Record<SpacingId, number> = { none: 0, s: 3, m: 6, l: 9 };

export const SIZE_LABELS: Record<SizeId, string> = { s: "S", m: "M", l: "L", xl: "XL" };

export const SPACING_LABELS: Record<SpacingId, string> = {
  none: "Ohne",
  s: "S",
  m: "M",
  l: "L",
};
