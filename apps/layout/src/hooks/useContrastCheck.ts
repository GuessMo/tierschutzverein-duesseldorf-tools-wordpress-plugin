import { useEffect, type RefObject } from "react";
import { resolveDimensions } from "@/config/dimensions";
import { measureContrast } from "@/services/contrast";
import { useContrastStore } from "@/store/contrastStore";
import type { LayoutDocument, Page } from "@/types/document";

const CHECK_DELAY_MS = 350;

async function imagesReady(artboard: HTMLElement): Promise<void> {
  const images = Array.from(artboard.querySelectorAll("img"));
  await Promise.all(images.map((image) => image.decode().catch(() => undefined)));
}

export function useContrastCheck(
  artboardRef: RefObject<HTMLDivElement | null>,
  document: LayoutDocument | null,
  page: Page | null,
  width: number,
): void {
  const setIssues = useContrastStore((state) => state.setIssues);

  useEffect(() => {
    const artboard = artboardRef.current;
    if (!artboard || !document || width === 0) return;
    let isCurrent = true;
    const timer = window.setTimeout(async () => {
      await imagesReady(artboard);
      if (isCurrent) setIssues(measureContrast(artboard, resolveDimensions(document)));
    }, CHECK_DELAY_MS);
    return () => {
      isCurrent = false;
      window.clearTimeout(timer);
    };
  }, [artboardRef, document, page, width, setIssues]);
}
