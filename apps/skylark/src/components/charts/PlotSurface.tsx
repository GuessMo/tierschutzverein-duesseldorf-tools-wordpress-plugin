import type { KeyboardEvent, PointerEvent, ReactNode } from "react";
import type { PlotFrame } from "./plotFrame";
import { CHART_HEIGHT, nearestIndex } from "./plotFrame";

interface PlotSurfaceProps {
  frame: PlotFrame;
  label: string;
  count: number;
  active: number | null;
  onActive: (index: number | null) => void;
  children: ReactNode;
}

const STEP_KEYS: Record<string, number> = { ArrowLeft: -1, ArrowRight: 1 };

const PLOT_CLASSES = [
  "block cursor-crosshair touch-pan-y rounded-md outline-none",
  "transition-colors hover:bg-surface-hover active:bg-surface-selected",
  "focus-visible:ring-2 focus-visible:ring-ring motion-reduce:transition-none",
].join(" ");

export function PlotSurface({ frame, label, count, active, onActive, children }: PlotSurfaceProps) {
  const onPointer = (event: PointerEvent<SVGSVGElement>) => {
    const box = event.currentTarget.getBoundingClientRect();
    onActive(nearestIndex(frame.xs, event.clientX - box.left));
  };
  const onKey = (event: KeyboardEvent<SVGSVGElement>) => {
    const step = STEP_KEYS[event.key];
    if (!step) return;
    event.preventDefault();
    const next = (active ?? (step > 0 ? -1 : count)) + step;
    onActive(Math.min(count - 1, Math.max(0, next)));
  };
  return (
    <svg
      width={frame.width}
      height={CHART_HEIGHT}
      role="img"
      aria-label={`${label}, Pfeiltasten zeigen einzelne Werte`}
      tabIndex={0}
      className={PLOT_CLASSES}
      onPointerMove={onPointer}
      onPointerLeave={() => onActive(null)}
      onKeyDown={onKey}
      onBlur={() => onActive(null)}
    >
      {children}
    </svg>
  );
}
