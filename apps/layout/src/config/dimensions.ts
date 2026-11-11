import { findFormat } from "@/config/formats";
import type { PageDimensions } from "@/types/catalog";
import type { LayoutDocument, OrientationId } from "@/types/document";

export const BLEED_MM = 3;
export const MARK_AREA_MM = 8;

function orient(first: number, second: number, orientation: OrientationId): [number, number] {
  const [short, long] = first <= second ? [first, second] : [second, first];
  return orientation === "landscape" ? [long, short] : [short, long];
}

export function resolveDimensions(document: Pick<LayoutDocument, "formatId" | "orientation">) {
  const format = findFormat(document.formatId);
  const isPrint = Boolean(format.widthMm && format.heightMm);
  const orientation = document.orientation ?? "portrait";
  const [widthPx, heightPx] = isPrint
    ? orient(format.widthPx, format.heightPx, orientation)
    : [format.widthPx, format.heightPx];
  const [widthMm, heightMm] = isPrint
    ? orient(format.widthMm ?? 0, format.heightMm ?? 0, orientation)
    : [0, 0];
  const bleedMm = isPrint ? BLEED_MM : 0;
  const dimensions: PageDimensions = {
    format,
    isPrint,
    widthPx,
    heightPx,
    widthMm,
    heightMm,
    bleedMm,
    outerRatio: isPrint ? (widthMm + 2 * bleedMm) / (heightMm + 2 * bleedMm) : widthPx / heightPx,
    bleedPercent: isPrint ? (bleedMm / (widthMm + 2 * bleedMm)) * 100 : 0,
  };
  return dimensions;
}
