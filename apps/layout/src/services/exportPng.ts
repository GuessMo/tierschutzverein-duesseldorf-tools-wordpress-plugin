import { toCanvas } from "html-to-image";
import type { PageDimensions } from "@/types/catalog";

export const EXPORTING_CLASS = "is-exporting";

function triggerDownload(dataUrl: string, fileName: string): void {
  const link = document.createElement("a");
  link.href = dataUrl;
  link.download = fileName;
  link.click();
}

export function toFileName(name: string, extension: string): string {
  const slug = name
    .trim()
    .toLowerCase()
    .replace(/[^a-z0-9äöüß]+/g, "-")
    .replace(/^-|-$/g, "");
  return `${slug || "layout"}.${extension}`;
}

function cropToTrim(source: HTMLCanvasElement, dimensions: PageDimensions): HTMLCanvasElement {
  if (!dimensions.isPrint) return source;
  const bleedPx = Math.round((dimensions.widthPx * dimensions.bleedMm) / dimensions.widthMm);
  const target = document.createElement("canvas");
  target.width = dimensions.widthPx;
  target.height = dimensions.heightPx;
  target
    .getContext("2d")
    ?.drawImage(
      source,
      bleedPx,
      bleedPx,
      target.width,
      target.height,
      0,
      0,
      target.width,
      target.height,
    );
  return target;
}

export async function exportPng(
  artboard: HTMLElement,
  dimensions: PageDimensions,
  fileName: string,
) {
  const outerWidthPx = dimensions.isPrint
    ? (dimensions.widthPx * (dimensions.widthMm + 2 * dimensions.bleedMm)) / dimensions.widthMm
    : dimensions.widthPx;
  artboard.classList.add(EXPORTING_CLASS);
  try {
    const canvas = await toCanvas(artboard, {
      pixelRatio: outerWidthPx / artboard.offsetWidth,
      cacheBust: true,
    });
    triggerDownload(cropToTrim(canvas, dimensions).toDataURL("image/png"), fileName);
  } finally {
    artboard.classList.remove(EXPORTING_CLASS);
  }
}
