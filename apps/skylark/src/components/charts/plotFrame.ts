import { niceTicks, type TickScale } from "@/utils";

export const CHART_HEIGHT = 220;
export const PADDING = { top: 12, right: 12, bottom: 28, left: 48 };

export interface PlotFrame {
  width: number;
  xs: number[];
  baseline: number;
  top: number;
  toY: (value: number) => number;
  ticks: { value: number; y: number }[];
  slot: number;
}

export function buildFrame(
  count: number,
  max: number,
  width: number,
  ticks: TickScale = niceTicks,
): PlotFrame {
  const plotWidth = Math.max(1, width - PADDING.left - PADDING.right);
  const plotHeight = CHART_HEIGHT - PADDING.top - PADDING.bottom;
  const values = ticks(Math.max(0, max));
  const top = values[values.length - 1] || 1;
  const baseline = PADDING.top + plotHeight;
  const toY = (value: number) => baseline - (value / top) * plotHeight;
  const slot = count > 0 ? plotWidth / count : plotWidth;
  const step = count > 1 ? plotWidth / (count - 1) : 0;
  const xs = Array.from(
    { length: count },
    (_, index) => PADDING.left + (count > 1 ? index * step : plotWidth / 2),
  );
  return {
    width,
    xs,
    baseline,
    top,
    toY,
    slot,
    ticks: values.map((value) => ({ value, y: toY(value) })),
  };
}

export function linePath(xs: number[], values: number[], toY: (value: number) => number): string {
  return values.map((value, index) => `${index ? "L" : "M"}${xs[index]},${toY(value)}`).join("");
}

export function areaPath(xs: number[], values: number[], frame: PlotFrame): string {
  const first = xs[0];
  const last = xs[values.length - 1];
  if (first === undefined || last === undefined) return "";
  return `${linePath(xs, values, frame.toY)}L${last},${frame.baseline}L${first},${frame.baseline}Z`;
}

export function nearestIndex(xs: number[], x: number): number {
  let best = 0;
  xs.forEach((value, index) => {
    if (Math.abs(value - x) < Math.abs((xs[best] ?? 0) - x)) best = index;
  });
  return best;
}

export function labelIndexes(count: number): number[] {
  if (count <= 1) return count ? [0] : [];
  return Array.from(new Set([0, Math.floor((count - 1) / 2), count - 1]));
}
