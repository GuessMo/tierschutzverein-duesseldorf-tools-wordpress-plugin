import type { Rgb } from "@/types/contrast";
import { blend, toRgba } from "./colorMath";

const SAMPLE_GRID = 12;

interface Overlay {
  color: Rgb;
  bottom: number;
  top: number;
  stop: number;
}

function readOverlay(section: HTMLElement): Overlay | null {
  if (!section.querySelector(".artboard-section__overlay")) return null;
  const style = getComputedStyle(section);
  const [red, green, blue] = toRgba(`rgb(${style.getPropertyValue("--overlay-rgb")})`);
  return {
    color: [red, green, blue],
    bottom: parseFloat(style.getPropertyValue("--overlay-bottom")),
    top: parseFloat(style.getPropertyValue("--overlay-top")),
    stop: parseFloat(style.getPropertyValue("--overlay-stop")) / 100,
  };
}

function overlayAlpha(overlay: Overlay, fractionFromBottom: number): number {
  if (fractionFromBottom >= overlay.stop) return overlay.top;
  return overlay.bottom + (overlay.top - overlay.bottom) * (fractionFromBottom / overlay.stop);
}

function sourceRect(image: HTMLImageElement, box: DOMRect, target: DOMRect) {
  const scale = Math.max(box.width / image.naturalWidth, box.height / image.naturalHeight);
  const offsetX = (box.width - image.naturalWidth * scale) / 2;
  const offsetY = (box.height - image.naturalHeight * scale) / 2;
  return {
    x: (target.left - box.left - offsetX) / scale,
    y: (target.top - box.top - offsetY) / scale,
    width: target.width / scale,
    height: target.height / scale,
  };
}

export function samplePhoto(section: HTMLElement, target: DOMRect): Rgb[] {
  const image = section.querySelector<HTMLImageElement>(".artboard-section__image");
  if (!image?.complete || image.naturalWidth === 0) return [];
  const box = section.getBoundingClientRect();
  const source = sourceRect(image, box, target);
  const canvas = document.createElement("canvas");
  canvas.width = SAMPLE_GRID;
  canvas.height = SAMPLE_GRID;
  const context = canvas.getContext("2d", { willReadFrequently: true });
  if (!context) return [];
  context.drawImage(
    image,
    source.x,
    source.y,
    source.width,
    source.height,
    0,
    0,
    SAMPLE_GRID,
    SAMPLE_GRID,
  );
  const pixels = context.getImageData(0, 0, SAMPLE_GRID, SAMPLE_GRID).data;
  const overlay = readOverlay(section);
  return Array.from({ length: SAMPLE_GRID * SAMPLE_GRID }, (_, index) => {
    const pixel: Rgb = [pixels[index * 4], pixels[index * 4 + 1], pixels[index * 4 + 2]];
    if (!overlay) return pixel;
    const row = Math.floor(index / SAMPLE_GRID);
    const pixelY = target.top + ((row + 0.5) / SAMPLE_GRID) * target.height;
    return blend(overlay.color, pixel, overlayAlpha(overlay, (box.bottom - pixelY) / box.height));
  });
}
