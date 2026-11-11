import { formatDay } from "@/utils";
import type { LineGeometry } from "./lineGeometry";
import { CHART_HEIGHT, PADDING, xLabelIndexes } from "./lineGeometry";

interface ChartAxesProps {
  geometry: LineGeometry;
  width: number;
  format: (value: number) => string;
}

const ANCHORS = ["start", "middle", "end"] as const;

export function ChartAxes({ geometry, width, format }: ChartAxesProps) {
  const labelIndexes = xLabelIndexes(geometry.points.length);
  return (
    <g aria-hidden className="text-xs">
      {geometry.ticks.map((tick) => (
        <g key={tick.value}>
          <line
            x1={PADDING.left}
            x2={width - PADDING.right}
            y1={tick.y}
            y2={tick.y}
            className="stroke-border"
          />
          <text
            x={PADDING.left - 8}
            y={tick.y}
            dy="0.32em"
            textAnchor="end"
            className="fill-muted-foreground"
          >
            {format(tick.value)}
          </text>
        </g>
      ))}
      {labelIndexes.map((index, position) => {
        const point = geometry.points[index];
        if (!point) return null;
        const anchor =
          labelIndexes.length === 1
            ? "middle"
            : ANCHORS[position === 0 ? 0 : position === labelIndexes.length - 1 ? 2 : 1];
        return (
          <text
            key={index}
            x={point.x}
            y={CHART_HEIGHT - 8}
            textAnchor={anchor}
            className="fill-muted-foreground"
          >
            {formatDay(point.row.label)}
          </text>
        );
      })}
    </g>
  );
}
