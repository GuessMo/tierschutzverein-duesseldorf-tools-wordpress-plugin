import type { CountRow } from "@/types";

const DAY_MS = 86_400_000;

function isoDay(time: number): string {
  return new Date(time).toISOString().slice(0, 10);
}

export function fillDays(rows: CountRow[], range: number, now = Date.now()): CountRow[] {
  const totals = new Map(rows.map((row) => [row.label, row.total]));
  const today = Date.parse(`${isoDay(now)}T00:00:00Z`);
  return Array.from({ length: range }, (_, index) => {
    const label = isoDay(today - (range - 1 - index) * DAY_MS);
    return { label, total: totals.get(label) ?? 0 };
  });
}
