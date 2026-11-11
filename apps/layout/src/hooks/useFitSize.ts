import { useLayoutEffect, useState, type RefObject } from "react";

const CANVAS_PADDING_PX = 48;

interface FitSize {
  width: number;
  height: number;
}

function fit(container: HTMLElement, aspectRatio: number): FitSize {
  const availableWidth = Math.max(container.clientWidth - CANVAS_PADDING_PX * 2, 120);
  const availableHeight = Math.max(container.clientHeight - CANVAS_PADDING_PX * 2, 120);
  const width = Math.min(availableWidth, availableHeight * aspectRatio);
  return { width: Math.round(width), height: Math.round(width / aspectRatio) };
}

export function useFitSize(
  containerRef: RefObject<HTMLElement | null>,
  aspectRatio: number,
): FitSize {
  const [size, setSize] = useState<FitSize>({ width: 0, height: 0 });

  useLayoutEffect(() => {
    const container = containerRef.current;
    if (!container) return;
    const update = () => setSize(fit(container, aspectRatio));
    update();
    const observer = new ResizeObserver(update);
    observer.observe(container);
    return () => observer.disconnect();
  }, [containerRef, aspectRatio]);

  return size;
}
