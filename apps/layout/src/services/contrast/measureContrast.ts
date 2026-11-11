import type { PageDimensions } from "@/types/catalog";
import type { ContrastIssue, Rgb } from "@/types/contrast";
import { contrastRatio, toRgba } from "./colorMath";
import { samplePhoto } from "./photoSampling";
import { requiredRatio } from "./textSize";

const TEXT_SELECTOR = ".el-headline, .el-text, .el-contact, .el-button, .el-badge, .el-logo__name";
const FALLBACK_BACKGROUND: Rgb = [255, 255, 255];

function backgroundsBehind(
  text: HTMLElement,
  artboard: HTMLElement,
): { colors: Rgb[]; isOnPhoto: boolean } {
  for (let node: HTMLElement | null = text; node; node = node.parentElement) {
    if (
      node.classList.contains("artboard-section") &&
      node.querySelector(".artboard-section__image")
    ) {
      return { colors: samplePhoto(node, text.getBoundingClientRect()), isOnPhoto: true };
    }
    const [red, green, blue, alpha] = toRgba(getComputedStyle(node).backgroundColor);
    if (alpha > 0.9) return { colors: [[red, green, blue]], isOnPhoto: false };
    if (node === artboard) break;
  }
  return { colors: [FALLBACK_BACKGROUND], isOnPhoto: false };
}

function measureText(text: HTMLElement, artboard: HTMLElement, dimensions: PageDimensions) {
  const wrapper = text.closest<HTMLElement>(".artboard-element");
  if (!wrapper || !text.textContent?.trim()) return null;
  const [red, green, blue] = toRgba(getComputedStyle(text).color);
  const { colors, isOnPhoto } = backgroundsBehind(text, artboard);
  if (colors.length === 0) return null;
  const ratio = Math.min(...colors.map((color) => contrastRatio([red, green, blue], color)));
  const required = requiredRatio(text, artboard.offsetWidth, dimensions);
  if (ratio >= required) return null;
  return { wrapper, ratio, required, isOnPhoto };
}

export function measureContrast(
  artboard: HTMLElement,
  dimensions: PageDimensions,
): ContrastIssue[] {
  const texts = Array.from(artboard.querySelectorAll<HTMLElement>(TEXT_SELECTOR));
  return texts.flatMap((text) => {
    const result = measureText(text, artboard, dimensions);
    if (!result) return [];
    const { sectionId = "", cellId = "", elementId = "" } = result.wrapper.dataset;
    const label = text.textContent?.trim().slice(0, 40) ?? "";
    return [
      {
        sectionId,
        cellId,
        elementId,
        label,
        ratio: result.ratio,
        required: result.required,
        isOnPhoto: result.isOnPhoto,
      },
    ];
  });
}
