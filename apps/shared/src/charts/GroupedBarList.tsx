import type { GroupedBarListProps } from "./charts.types";
import { ChartLegend } from "./ChartLegend";
import { EmptyNote } from "./EmptyNote";

const VALUE_CLASSES = "w-24 shrink-0 text-right text-xs tabular-nums text-muted-foreground";

export function GroupedBarList(props: GroupedBarListProps) {
  const { rows, series, label, format, rowLabel = (value) => value } = props;
  if (rows.length === 0) return <EmptyNote />;
  const max = Math.max(1, ...rows.flatMap((row) => row.values));
  return (
    <div className="flex flex-col gap-4">
      <ChartLegend items={series} />
      <ol aria-label={label} className="flex flex-col gap-3">
        {rows.map((row, rowIndex) => (
          <li key={row.label} className="flex flex-col gap-1">
            <span className="truncate text-sm">{rowLabel(row.label)}</span>
            {series.map((entry, index) => (
              <div key={entry.label} className="flex items-center gap-2">
                <div aria-hidden className="h-2 flex-1 rounded-full bg-muted">
                  <div
                    className="chart-grow-x h-2 rounded-full"
                    style={{
                      width: `${((row.values[index] ?? 0) / max) * 100}%`,
                      background: entry.color,
                      animationDelay: `${Math.min(rowIndex * 30, 400)}ms`,
                    }}
                  />
                </div>
                <span className={VALUE_CLASSES}>
                  <span className="sr-only">{entry.label}: </span>
                  {format(row.values[index] ?? 0)}
                </span>
              </div>
            ))}
          </li>
        ))}
      </ol>
    </div>
  );
}
