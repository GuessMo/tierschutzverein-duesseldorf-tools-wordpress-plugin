import type { PlotFrame } from "./plotFrame";
import { CHART_HEIGHT, labelIndexes, PADDING } from "./plotFrame";

interface PlotAxesProps {
  frame: PlotFrame;
  labels: string[];
  format: (value: number) => string;
  grid: boolean;
  showAll?: boolean;
}

function anchorFor(position: number, total: number) {
  if (total === 1) return "middle";
  if (position === 0) return "start";
  return position === total - 1 ? "end" : "middle";
}

export function PlotAxes({ frame, labels, format, grid, showAll = false }: PlotAxesProps) {
  const indexes = showAll ? labels.map((_, index) => index) : labelIndexes(labels.length);
  return (
    <g aria-hidden className="text-xs">
      {frame.ticks.map((tick) => (
        <g key={tick.value}>
          {(grid || tick.value === 0) && (
            <line
              x1={PADDING.left}
              x2={frame.width - PADDING.right}
              y1={tick.y}
              y2={tick.y}
              className="stroke-border"
            />
          )}
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
      {indexes.map((index, position) => (
        <text
          key={index}
          x={frame.xs[index]}
          y={CHART_HEIGHT - 8}
          textAnchor={showAll ? "middle" : anchorFor(position, indexes.length)}
          className="fill-muted-foreground"
        >
          {labels[index]}
        </text>
      ))}
    </g>
  );
}
