import { formatNumber } from "@/utils";
import { seriesStyle } from "./seriesStyle";
import type { BarListProps } from "./charts.types";

export function BarList({
  rows,
  label,
  series,
  emptyText = "Keine Daten im Zeitraum.",
}: BarListProps) {
  if (rows.length === 0) return <p className="text-sm text-muted-foreground">{emptyText}</p>;
  const max = Math.max(...rows.map((row) => row.total), 1);
  return (
    <ol aria-label={label} className="flex flex-col gap-2" style={seriesStyle(series)}>
      {rows.map((row) => (
        <li key={row.label} className="flex flex-col gap-1">
          <div className="flex items-baseline justify-between gap-4 text-sm">
            <span className="min-w-0 truncate" title={row.label}>
              {row.label}
            </span>
            <span className="shrink-0 tabular-nums text-muted-foreground">
              {formatNumber(row.total)}
            </span>
          </div>
          <div className="h-2 rounded-full bg-muted" aria-hidden>
            <div
              className="h-2 rounded-full bg-(--series)"
              style={{ width: `${(row.total / max) * 100}%` }}
            />
          </div>
        </li>
      ))}
    </ol>
  );
}
