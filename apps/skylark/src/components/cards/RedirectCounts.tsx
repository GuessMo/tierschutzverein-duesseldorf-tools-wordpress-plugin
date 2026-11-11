import { ChartView, ViewToggle, monthLabel, useView } from "@tsvd/tool-controls";
import type { RedirectCounts as Counts } from "@/types";
import { formatNumber } from "@tsvd/tool-controls";

const VIEWS = [
  { value: "month", label: "Monat" },
  { value: "months", label: "Monate" },
  { value: "years", label: "Jahre" },
  { value: "all", label: "Gesamt" },
];

const COLUMN_PREFS = { type: "vbars", grid: true, colored: false } as const;

function BigNumber({ value, caption }: { value: number; caption: string }) {
  return (
    <p className="chart-fade flex items-baseline gap-2 py-6">
      <span className="text-4xl font-semibold tabular-nums">{formatNumber(value)}</span>
      <span className="text-sm text-muted-foreground">{caption}</span>
    </p>
  );
}

export function RedirectCounts({
  id,
  counts,
  color,
}: {
  id: string;
  counts: Counts;
  color: string;
}) {
  const [view, setView] = useView(`${id}-counts`, VIEWS);
  const rows = view === "months" ? counts.months : counts.years;
  return (
    <div className="flex flex-col gap-3">
      <ViewToggle label="Zeitraum der Zählung" options={VIEWS} value={view} onChange={setView} />
      {view === "month" && (
        <BigNumber value={counts.month} caption="Weiterleitungen in diesem Monat" />
      )}
      {view === "all" && <BigNumber value={counts.all} caption="Weiterleitungen insgesamt" />}
      {(view === "months" || view === "years") && (
        <ChartView
          key={view}
          rows={rows}
          prefs={COLUMN_PREFS}
          color={color}
          label={view === "months" ? "Weiterleitungen je Monat" : "Weiterleitungen je Jahr"}
          format={formatNumber}
          rowLabel={view === "months" ? monthLabel : undefined}
        />
      )}
    </div>
  );
}
