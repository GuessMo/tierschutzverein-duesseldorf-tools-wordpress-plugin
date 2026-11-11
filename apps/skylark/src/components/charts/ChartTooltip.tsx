import { formatDay } from "@/utils";
import type { ChartPoint } from "./charts.types";

const TOOLTIP_CLASSES = [
  "pointer-events-none absolute top-0 flex flex-col",
  "gap-0.5 rounded-md border bg-popover px-3",
  "py-2 text-xs text-popover-foreground shadow-sm",
].join(" ");

interface ChartTooltipProps {
  point: ChartPoint;
  width: number;
  format: (value: number) => string;
}

export function ChartTooltip({ point, width, format }: ChartTooltipProps) {
  const alignRight = point.x > width / 2;
  return (
    <div
      aria-live="polite"
      className={TOOLTIP_CLASSES}
      style={alignRight ? { right: width - point.x + 12 } : { left: point.x + 12 }}
    >
      <span className="text-muted-foreground">{formatDay(point.row.label)}</span>
      <span className="text-sm font-medium tabular-nums">{format(point.row.total)}</span>
    </div>
  );
}
