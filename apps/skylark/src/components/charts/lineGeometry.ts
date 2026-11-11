import type { CountRow } from "@/types";
import { niceTicks, type TickScale } from "@/utils";
import type { ChartPoint } from "./charts.types";

export const CHART_HEIGHT = 200;
export const PADDING = { top: 12, right: 12, bottom: 28, left: 48 };

export interface LineGeometry {
  points: ChartPoint[];
  ticks: { value: number; y: number }[];
  linePath: string;
  areaPath: string;
  baseline: number;
}

export function buildGeometry(
  rows: CountRow[],
  width: number,
  ticks: TickScale = niceTicks,
): LineGeometry {
  const plotWidth = Math.max(1, width - PADDING.left - PADDING.right);
  const plotHeight = CHART_HEIGHT - PADDING.top - PADDING.bottom;
  const values = ticks(Math.max(...rows.map((row) => row.total), 0));
  const top = values[values.length - 1] ?? 1;
  const baseline = PADDING.top + plotHeight;
  const toY = (value: number) => baseline - (value / top) * plotHeight;
  const step = rows.length > 1 ? plotWidth / (rows.length - 1) : 0;
  const points = rows.map((row, index) => ({
    x: PADDING.left + (rows.length > 1 ? index * step : plotWidth / 2),
    y: toY(row.total),
    row,
  }));
  const linePath = points
    .map((point, index) => `${index ? "L" : "M"}${point.x},${point.y}`)
    .join("");
  const first = points[0];
  const last = points[points.length - 1];
  const areaPath = first && last ? `${linePath}L${last.x},${baseline}L${first.x},${baseline}Z` : "";
  return {
    points,
    ticks: values.map((value) => ({ value, y: toY(value) })),
    linePath,
    areaPath,
    baseline,
  };
}

export function nearestIndex(points: ChartPoint[], x: number): number {
  let best = 0;
  points.forEach((point, index) => {
    if (Math.abs(point.x - x) < Math.abs((points[best]?.x ?? 0) - x)) best = index;
  });
  return best;
}

export function xLabelIndexes(count: number): number[] {
  if (count <= 1) return count ? [0] : [];
  return Array.from(new Set([0, Math.floor((count - 1) / 2), count - 1]));
}
