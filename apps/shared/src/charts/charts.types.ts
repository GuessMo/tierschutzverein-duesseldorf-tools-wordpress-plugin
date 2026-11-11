import type { CountRow } from "../types";
import type { TickScale } from "../utils";

export type SeriesSlot = 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8;

export interface PlotSeries {
  key: string;
  label: string;
  color: string;
  values: number[];
}

export interface TooltipEntry {
  label: string;
  value: string;
  color: string;
}

export interface MultiLineChartProps {
  labels: string[];
  series: PlotSeries[];
  format: (value: number) => string;
  axisFormat?: (value: number) => string;
  ticks?: TickScale;
  grid: boolean;
  label: string;
  area?: boolean;
}

export interface CategoryChartProps {
  rows: CountRow[];
  label: string;
  color: string;
  colored: boolean;
  grid: boolean;
  format: (value: number) => string;
  rowLabel?: (label: string) => string;
}

export interface StatTileProps {
  label: string;
  value: string;
  sub?: string;
}

export interface GroupedRow {
  label: string;
  values: number[];
}

export interface GroupedBarListProps {
  rows: GroupedRow[];
  series: { label: string; color: string }[];
  label: string;
  format: (value: number) => string;
  rowLabel?: (label: string) => string;
}
