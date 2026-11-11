import { useState } from "react";
import { useElementWidth } from "@/hooks";
import type { CategoryChartProps } from "./charts.types";
import { EmptyNote } from "./EmptyNote";
import { NumberedLegend } from "./NumberedLegend";
import { categoryColor } from "./palette";
import { PlotAxes } from "./PlotAxes";
import { PlotSurface } from "./PlotSurface";
import { PlotTooltip } from "./PlotTooltip";
import { buildFrame, PADDING } from "./plotFrame";

const MAX_BAR = 24;
const DATE_LABEL = /^\d{4}-\d{2}/;

export function ColumnChart(props: CategoryChartProps) {
  const { rows, label, color, colored, grid, format, rowLabel = (value) => value } = props;
  const { ref, width } = useElementWidth<HTMLDivElement>();
  const [active, setActive] = useState<number | null>(null);
  if (rows.length === 0) return <EmptyNote />;
  const frame = buildFrame(rows.length, Math.max(...rows.map((row) => row.total)), width);
  const inner = Math.max(1, width - PADDING.left - PADDING.right);
  const slot = inner / rows.length;
  const barWidth = Math.max(2, Math.min(MAX_BAR, slot - 2));
  const xs = rows.map((_, index) => PADDING.left + slot * index + slot / 2);
  const axisFrame = { ...frame, xs };
  const activeRow = active === null ? null : rows[active];
  const isNumbered = !rows.every((row) => DATE_LABEL.test(row.label));
  const axisLabels = rows.map((row, index) =>
    isNumbered ? String(index + 1) : rowLabel(row.label),
  );
  return (
    <div className="flex flex-col gap-3">
      <div ref={ref} className="relative">
        {width > 0 && (
          <PlotSurface
            frame={axisFrame}
            label={label}
            count={rows.length}
            active={active}
            onActive={setActive}
          >
            <PlotAxes
              frame={axisFrame}
              labels={axisLabels}
              format={format}
              grid={grid}
              showAll={isNumbered}
            />
            {rows.map((row, index) => {
              const y = frame.toY(row.total);
              const height = Math.max(0, frame.baseline - y);
              return (
                <path
                  key={row.label}
                  d={columnPath(xs[index] ?? 0, barWidth, y, height)}
                  className="chart-grow-y transition-opacity"
                  style={{
                    fill: categoryColor(colored ? index : 0, color, colored),
                    opacity: active === null || active === index ? 1 : 0.55,
                    animationDelay: `${Math.min(index * 25, 400)}ms`,
                  }}
                />
              );
            })}
          </PlotSurface>
        )}
        {activeRow && active !== null && (
          <PlotTooltip
            x={xs[active] ?? 0}
            width={width}
            title={rowLabel(activeRow.label)}
            entries={[{ label: label, value: format(activeRow.total), color }]}
          />
        )}
      </div>
      {isNumbered && <NumberedLegend rows={rows} format={format} rowLabel={rowLabel} />}
    </div>
  );
}

function columnPath(center: number, width: number, top: number, height: number): string {
  const left = center - width / 2;
  const radius = Math.min(4, width / 2, height);
  const bottom = top + height;
  return [
    `M${left},${bottom}`,
    `V${top + radius}`,
    `Q${left},${top} ${left + radius},${top}`,
    `H${left + width - radius}`,
    `Q${left + width},${top} ${left + width},${top + radius}`,
    `V${bottom}Z`,
  ].join("");
}
