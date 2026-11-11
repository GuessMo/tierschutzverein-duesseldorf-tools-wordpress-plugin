import type { CountRow } from "@/types";

interface NumberedLegendProps {
  rows: CountRow[];
  format: (value: number) => string;
  rowLabel: (label: string) => string;
}

export function NumberedLegend({ rows, format, rowLabel }: NumberedLegendProps) {
  return (
    <ol className="grid gap-x-6 gap-y-1 text-xs text-muted-foreground sm:grid-cols-2">
      {rows.map((row, index) => (
        <li key={row.label} className="flex min-w-0 gap-2">
          <span className="w-5 shrink-0 text-right tabular-nums">{index + 1}</span>
          <span className="min-w-0 flex-1 truncate" title={rowLabel(row.label)}>
            {rowLabel(row.label)}
          </span>
          <span className="shrink-0 tabular-nums text-foreground">{format(row.total)}</span>
        </li>
      ))}
    </ol>
  );
}
