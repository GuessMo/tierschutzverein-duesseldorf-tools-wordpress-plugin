import type { ReactNode } from "react";
import type { CountRow } from "@/types";
import type { TickScale } from "@/utils";

export type SeriesSlot = 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8;

export interface LineChartProps {
  rows: CountRow[];
  label: string;
  format: (value: number) => string;
  axisFormat?: (value: number) => string;
  ticks?: TickScale;
  series: SeriesSlot;
}

export interface BarListProps {
  rows: CountRow[];
  label: string;
  emptyText?: string;
  series: SeriesSlot;
}

export interface ChartCardProps {
  title: string;
  description?: string;
  action?: ReactNode;
  children: ReactNode;
  className?: string;
}

export interface StatTileProps {
  label: string;
  value: string;
}

export interface ChartPoint {
  x: number;
  y: number;
  row: CountRow;
}
