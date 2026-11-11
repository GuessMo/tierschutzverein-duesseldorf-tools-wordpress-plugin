import type { SeriesSlot } from "./charts.types";

export function slotColor(slot: SeriesSlot): string {
  return `var(--series-${slot})`;
}

export function categoryColor(index: number, base: string, colored: boolean): string {
  if (colored) return `var(--series-${(index % 8) + 1})`;
  const mix = Math.max(35, 100 - index * 9);
  return `color-mix(in oklab, ${base} ${mix}%, var(--card))`;
}

export const NEUTRAL = "var(--muted-foreground)";
