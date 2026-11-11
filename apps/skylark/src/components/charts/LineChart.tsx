import { useState, type KeyboardEvent, type PointerEvent } from "react";
import { useElementWidth } from "@/hooks";
import type { LineChartProps } from "./charts.types";
import { ChartAxes } from "./ChartAxes";
import { ChartTooltip } from "./ChartTooltip";
import { DataTable } from "./DataTable";
import { buildGeometry, CHART_HEIGHT, nearestIndex } from "./lineGeometry";
import { seriesStyle } from "./seriesStyle";

const PLOT_CLASSES = [
  "block cursor-crosshair touch-pan-y rounded-md outline-none",
  "transition-colors hover:bg-surface-hover active:bg-surface-selected",
  "focus-visible:ring-2 focus-visible:ring-ring",
  "motion-reduce:transition-none",
].join(" ");

const STEP_KEYS: Record<string, number> = { ArrowLeft: -1, ArrowRight: 1 };

export function LineChart(props: LineChartProps) {
  const { rows, label, format, axisFormat = format, ticks, series } = props;
  const { ref, width } = useElementWidth<HTMLDivElement>();
  const [active, setActive] = useState<number | null>(null);
  if (rows.length === 0) {
    return <p className="text-sm text-muted-foreground">Keine Daten im Zeitraum.</p>;
  }
  const geometry = buildGeometry(rows, width, ticks);
  const point = active === null ? null : geometry.points[active];
  const onPointer = (event: PointerEvent<SVGSVGElement>) => {
    const box = event.currentTarget.getBoundingClientRect();
    setActive(nearestIndex(geometry.points, event.clientX - box.left));
  };
  const onKey = (event: KeyboardEvent<SVGSVGElement>) => {
    const step = STEP_KEYS[event.key];
    if (!step) return;
    event.preventDefault();
    const next = (active ?? (step > 0 ? -1 : rows.length)) + step;
    setActive(Math.min(rows.length - 1, Math.max(0, next)));
  };
  return (
    <div ref={ref} className="relative" style={seriesStyle(series)}>
      {width > 0 && (
        <svg
          width={width}
          height={CHART_HEIGHT}
          role="img"
          aria-label={`${label}, Pfeiltasten zeigen einzelne Tage`}
          tabIndex={0}
          className={PLOT_CLASSES}
          onPointerMove={onPointer}
          onPointerLeave={() => setActive(null)}
          onKeyDown={onKey}
          onBlur={() => setActive(null)}
        >
          <ChartAxes geometry={geometry} width={width} format={axisFormat} />
          <path d={geometry.areaPath} className="fill-(--series)/10" />
          <path
            d={geometry.linePath}
            className="fill-none stroke-(--series)"
            strokeWidth={2}
            strokeLinejoin="round"
            strokeLinecap="round"
          />
          {point && (
            <g aria-hidden>
              <line
                x1={point.x}
                x2={point.x}
                y1={12}
                y2={geometry.baseline}
                className="stroke-muted-foreground/50"
              />
              <circle
                cx={point.x}
                cy={point.y}
                r={5}
                className="fill-(--series) stroke-card"
                strokeWidth={2}
              />
            </g>
          )}
        </svg>
      )}
      {point && <ChartTooltip point={point} width={width} format={format} />}
      <DataTable rows={rows} label={label} format={format} />
    </div>
  );
}
