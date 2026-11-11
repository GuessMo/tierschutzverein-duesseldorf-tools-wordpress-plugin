import type { PageDimensions } from "@/types/catalog";

const SOCIAL_VIEWPORT_CSS_PX = 390;
const CSS_PX_PER_MM = 96 / 25.4;
const LARGE_TEXT_PX = 24;
const LARGE_BOLD_TEXT_PX = 18.66;
const BOLD_WEIGHT = 700;

function referenceWidth(dimensions: PageDimensions): number {
  if (dimensions.isPrint) return dimensions.widthMm * CSS_PX_PER_MM;
  return SOCIAL_VIEWPORT_CSS_PX;
}

function trimWidth(artboardWidth: number, dimensions: PageDimensions): number {
  return artboardWidth * (1 - (2 * dimensions.bleedPercent) / 100);
}

export function requiredRatio(
  element: HTMLElement,
  artboardWidth: number,
  dimensions: PageDimensions,
) {
  const style = getComputedStyle(element);
  const cssPx =
    (parseFloat(style.fontSize) * referenceWidth(dimensions)) /
    trimWidth(artboardWidth, dimensions);
  const isBold = parseInt(style.fontWeight, 10) >= BOLD_WEIGHT;
  const isLarge = cssPx >= LARGE_TEXT_PX || (isBold && cssPx >= LARGE_BOLD_TEXT_PX);
  return isLarge ? 3 : 4.5;
}
