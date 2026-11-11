import { useState } from "react";
import { useElementWidth } from "@/hooks";
import type { MultiLineChartProps } from "./charts.types";
import { ChartLegend } from "./ChartLegend";
import { EmptyNote } from "./EmptyNote";
import { PlotAxes } from "./PlotAxes";
import { PlotSurface } from "./PlotSurface";
import { PlotTooltip } from "./PlotTooltip";
import { SeriesTable } from "./SeriesTable";
import { areaPath, buildFrame, linePath } from "./plotFrame";

export function MultiLineChart(props: MultiLineChartProps) {
  const { labels, series, format, axisFormat = format, ticks, grid, label, area = false } = props;
  const { ref, width } = useElementWidth<HTMLDivElement>();
  const [active, setActive] = useState<number | null>(null);
  if (labels.length === 0) return <EmptyNote />;
  const max = Math.max(0, ...series.flatMap((entry) => entry.values));
  const frame = buildFrame(labels.length, max, width, ticks);
  const x = active === null ? null : frame.xs[active];
  return (
    <div className="flex flex-col gap-3">
      <div ref={ref} className="relative">
        {width > 0 && (
          <PlotSurface
            frame={frame}
            label={label}
            count={labels.length}
            onActive={setActive}
            active={active}
          >
            <PlotAxes frame={frame} labels={labels} format={axisFormat} grid={grid} />
            {series.map((entry) => (
              <g key={entry.key} style={{ color: entry.color }}>
                {area && (
                  <path
                    d={areaPath(frame.xs, entry.values, frame)}
                    className="chart-fade fill-current opacity-10"
                  />
                )}
                <path
                  d={linePath(frame.xs, entry.values, frame.toY)}
                  pathLength={1}
                  className="chart-draw fill-none stroke-current"
                  strokeWidth={2}
                  strokeLinejoin="round"
                  strokeLinecap="round"
                />
              </g>
            ))}
            {x !== undefined && x !== null && active !== null && (
              <g aria-hidden>
                <line
                  x1={x}
                  x2={x}
                  y1={12}
                  y2={frame.baseline}
                  className="stroke-muted-foreground/50"
                />
                {series.map((entry) => (
                  <circle
                    key={entry.key}
                    cx={x}
                    cy={frame.toY(entry.values[active] ?? 0)}
                    r={5}
                    className="stroke-card"
                    strokeWidth={2}
                    style={{ fill: entry.color }}
                  />
                ))}
              </g>
            )}
          </PlotSurface>
        )}
        {active !== null && x !== undefined && x !== null && (
          <PlotTooltip
            x={x}
            width={width}
            title={labels[active] ?? ""}
            entries={series.map((entry) => ({
              label: entry.label,
              color: entry.color,
              value: format(entry.values[active] ?? 0),
            }))}
          />
        )}
        <SeriesTable labels={labels} series={series} format={format} caption={label} />
      </div>
      {series.length > 1 && (
        <ChartLegend items={series.map(({ label: name, color }) => ({ label: name, color }))} />
      )}
    </div>
  );
}
