import type { CSSProperties } from "react";
import type { SeriesSlot } from "./charts.types";

export function seriesStyle(slot: SeriesSlot): CSSProperties {
  return { "--series": `var(--series-${slot})` } as CSSProperties;
}
