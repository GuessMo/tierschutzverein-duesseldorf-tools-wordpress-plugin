import { EmptyNote } from "@/components/charts";
import type { CountRow } from "@/types";
import { formatNumber } from "@/utils";

interface TopListProps {
  rows: CountRow[];
  empty?: string;
  showTotals?: boolean;
}

export function TopList({ rows, empty, showTotals = true }: TopListProps) {
  if (rows.length === 0) return <EmptyNote text={empty} />;
  return (
    <ol className="flex flex-col divide-y text-sm">
      {rows.map((row, index) => (
        <li
          key={`${row.label}-${index}`}
          className="chart-fade flex items-baseline justify-between gap-4 py-2"
        >
          <span className="min-w-0 truncate">{row.label}</span>
          {showTotals && (
            <span className="shrink-0 tabular-nums text-muted-foreground">
              {formatNumber(row.total)}
            </span>
          )}
        </li>
      ))}
    </ol>
  );
}
