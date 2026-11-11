import type { CategoryChartProps } from "./charts.types";
import { EmptyNote } from "./EmptyNote";
import { categoryColor } from "./palette";

export function BarList(props: CategoryChartProps) {
  const { rows, label, color, colored, grid, format, rowLabel = (value) => value } = props;
  if (rows.length === 0) return <EmptyNote />;
  const max = Math.max(...rows.map((row) => row.total), 1);
  return (
    <ol aria-label={label} className="flex flex-col gap-2.5">
      {rows.map((row, index) => (
        <li key={row.label} className="flex flex-col gap-1">
          <div className="flex items-baseline justify-between gap-4 text-sm">
            <span className="min-w-0 truncate" title={rowLabel(row.label)}>
              {rowLabel(row.label)}
            </span>
            <span className="shrink-0 tabular-nums text-muted-foreground">{format(row.total)}</span>
          </div>
          <div aria-hidden className={grid ? "h-2 rounded-full bg-muted" : "h-2"}>
            <div
              className="chart-grow-x h-2 rounded-full"
              style={{
                width: `${(row.total / max) * 100}%`,
                background: categoryColor(colored ? index : 0, color, colored),
                animationDelay: `${Math.min(index * 30, 400)}ms`,
              }}
            />
          </div>
        </li>
      ))}
    </ol>
  );
}
