import { MARK_AREA_MM } from "@/config/dimensions";
import { usePrintStore } from "@/store/printStore";
import type { PageDimensions } from "@/types/catalog";

const PAGE_STYLE_ID = "tsvd-print-page";
const PRINTING_ATTRIBUTE = "data-printing";

export function supportsPdf(dimensions: PageDimensions): boolean {
  return dimensions.isPrint;
}

export function sheetSize(dimensions: PageDimensions): { width: number; height: number } {
  const margin = 2 * (dimensions.bleedMm + MARK_AREA_MM);
  return { width: dimensions.widthMm + margin, height: dimensions.heightMm + margin };
}

function installPageStyle(dimensions: PageDimensions): void {
  const { width, height } = sheetSize(dimensions);
  const style =
    document.getElementById(PAGE_STYLE_ID) ??
    document.head.appendChild(document.createElement("style"));
  style.id = PAGE_STYLE_ID;
  style.textContent = `@page { size: ${width}mm ${height}mm; margin: 0; }`;
}

async function waitForSheets(): Promise<void> {
  await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));
  const images = Array.from(document.querySelectorAll<HTMLImageElement>(".print-sheets img"));
  await Promise.all(images.map((image) => image.decode().catch(() => undefined)));
}

export async function exportPdf(dimensions: PageDimensions, title: string): Promise<void> {
  const { setPrinting } = usePrintStore.getState();
  installPageStyle(dimensions);
  const previousTitle = document.title;
  document.title = title;
  document.documentElement.setAttribute(PRINTING_ATTRIBUTE, "");
  setPrinting(true);
  const cleanUp = () => {
    document.documentElement.removeAttribute(PRINTING_ATTRIBUTE);
    document.title = previousTitle;
    setPrinting(false);
    window.removeEventListener("afterprint", cleanUp);
  };
  window.addEventListener("afterprint", cleanUp);
  await waitForSheets();
  window.print();
}
