import type { ChartType } from "@/components/charts";
import type { ComparePair, CountRow } from "@/types";
import type { TickScale } from "@/utils";

export interface ChartPanelSpec {
  id: string;
  rows: CountRow[];
  types: ChartType[];
  fallback: ChartType;
  color: string;
  label: string;
  format: (value: number) => string;
  axisFormat?: (value: number) => string;
  ticks?: TickScale;
  rowLabel?: (label: string) => string;
  mapUrl?: string;
}

export interface MetricCardProps extends Omit<ChartPanelSpec, "types" | "fallback"> {
  title: string;
  description?: string;
  pairs: ComparePair[];
  className?: string;
}

export type ViewPanel =
  | { value: string; label: string; kind: "chart"; chart: ChartPanelSpec }
  | {
      value: string;
      label: string;
      kind: "list";
      rows: CountRow[];
      empty?: string;
      showTotals?: boolean;
    };

export interface MultiViewCardProps {
  id: string;
  title: string;
  description?: string;
  className?: string;
  panels: ViewPanel[];
}
