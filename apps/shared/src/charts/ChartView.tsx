import type { CountRow } from "../types";
import type { TickScale } from "../utils";
import { BarList } from "./BarList";
import type { ChartPrefs } from "./chartTypes";
import { isColored } from "./chartTypes";
import { ColumnChart } from "./ColumnChart";
import { DonutChart } from "./DonutChart";
import { MapChart } from "./MapChart";
import { MultiLineChart } from "./MultiLineChart";

export interface ChartViewProps {
  rows: CountRow[];
  prefs: ChartPrefs;
  color: string;
  label: string;
  format: (value: number) => string;
  axisFormat?: (value: number) => string;
  ticks?: TickScale;
  rowLabel?: (label: string) => string;
  mapUrl?: string;
}

export function ChartView({
  rows,
  prefs,
  color,
  label,
  format,
  axisFormat,
  ticks,
  rowLabel,
  mapUrl,
}: ChartViewProps) {
  const shared = {
    rows,
    label,
    color,
    format,
    rowLabel,
    grid: prefs.grid,
    colored: isColored(prefs),
  };
  if (prefs.type === "line") {
    const labels = rows.map((row) => (rowLabel ? rowLabel(row.label) : row.label));
    const series = [{ key: "value", label, color, values: rows.map((row) => row.total) }];
    return (
      <MultiLineChart
        labels={labels}
        series={series}
        format={format}
        axisFormat={axisFormat}
        ticks={ticks}
        grid={prefs.grid}
        label={label}
        area
      />
    );
  }
  if (prefs.type === "vbars") return <ColumnChart {...shared} />;
  if (prefs.type === "donut") return <DonutChart {...shared} inner={0.6} />;
  if (prefs.type === "pie") return <DonutChart {...shared} inner={0} />;
  if (prefs.type === "map" && mapUrl)
    return <MapChart rows={rows} url={mapUrl} color={color} format={format} label={label} />;
  return <BarList {...shared} />;
}
