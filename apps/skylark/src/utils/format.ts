const NUMBER = new Intl.NumberFormat("de-DE");
const COMPACT = new Intl.NumberFormat("de-DE", { notation: "compact", maximumFractionDigits: 1 });

export function formatNumber(value: number): string {
  return NUMBER.format(value);
}

export function formatCompact(value: number): string {
  return value < 10000 ? NUMBER.format(value) : COMPACT.format(value);
}

export function formatDuration(seconds: number): string {
  const safe = Math.max(0, Math.round(seconds));
  return `${Math.floor(safe / 60)}:${String(safe % 60).padStart(2, "0")}`;
}

export function formatDay(label: string): string {
  const [year, month, day] = label.split("-");
  if (!year || !month || !day) return label;
  return `${day}.${month}.`;
}
