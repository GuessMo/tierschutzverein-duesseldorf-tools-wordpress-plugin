import { useState } from "react";
import type { CategoryChartProps } from "./charts.types";
import { ChartLegend } from "./ChartLegend";
import { EmptyNote } from "./EmptyNote";
import { arcPath, foldRows } from "./donutGeometry";
import { categoryColor } from "./palette";

interface DonutChartProps extends CategoryChartProps {
  inner: number;
}

const SIZE = 200;

export function DonutChart({
  rows,
  label,
  color,
  colored,
  format,
  rowLabel = (v) => v,
  inner,
}: DonutChartProps) {
  const [active, setActive] = useState<number | null>(null);
  const slices = foldRows(rows);
  const total = slices.reduce((sum, row) => sum + row.total, 0);
  if (total <= 0) return <EmptyNote />;
  let start = 0;
  const arcs = slices.map((row, index) => {
    const sweep = (row.total / total) * Math.PI * 2;
    const arc = {
      row,
      index,
      start,
      end: start + sweep,
      fill: categoryColor(index, color, colored),
    };
    start += sweep;
    return arc;
  });
  const focus = active === null ? null : arcs[active];
  return (
    <div className="flex flex-col items-center gap-4 sm:flex-row sm:items-center">
      <svg
        width={SIZE}
        height={SIZE}
        viewBox={`0 0 ${SIZE} ${SIZE}`}
        role="img"
        aria-label={label}
        className="shrink-0"
      >
        <g className="chart-spin">
          {arcs.map((arc) => (
            <path
              key={arc.row.label}
              d={arcPath(SIZE / 2, SIZE / 2 - 4, (SIZE / 2 - 4) * inner, arc.start, arc.end)}
              className="stroke-card transition-opacity"
              strokeWidth={2}
              style={{
                fill: arc.fill,
                opacity: active === null || active === arc.index ? 1 : 0.45,
              }}
              onPointerEnter={() => setActive(arc.index)}
              onPointerLeave={() => setActive(null)}
            />
          ))}
        </g>
        {inner > 0 && (
          <text
            x={SIZE / 2}
            y={SIZE / 2}
            textAnchor="middle"
            dy="0.35em"
            className="fill-foreground text-sm font-medium"
          >
            {focus ? `${Math.round((focus.row.total / total) * 100)} %` : format(total)}
          </text>
        )}
      </svg>
      <ChartLegend
        items={arcs.map((arc) => ({
          label: rowLabel(arc.row.label),
          color: arc.fill,
          value: `${format(arc.row.total)} · ${Math.round((arc.row.total / total) * 100)} %`,
        }))}
      />
    </div>
  );
}
