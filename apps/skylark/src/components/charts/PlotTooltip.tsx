import type { TooltipEntry } from "./charts.types";

interface PlotTooltipProps {
  x: number;
  width: number;
  title: string;
  entries: TooltipEntry[];
}

const TOOLTIP_CLASSES = [
  "pointer-events-none absolute top-0 z-10 flex min-w-28 flex-col gap-1 rounded-md border",
  "bg-popover px-3 py-2 text-xs text-popover-foreground shadow-sm",
].join(" ");

export function PlotTooltip({ x, width, title, entries }: PlotTooltipProps) {
  const position = x > width / 2 ? { right: width - x + 12 } : { left: x + 12 };
  return (
    <div aria-live="polite" className={TOOLTIP_CLASSES} style={position}>
      <span className="text-muted-foreground">{title}</span>
      {entries.map((entry) => (
        <span key={entry.label} className="flex items-center gap-2">
          <span aria-hidden className="size-2 rounded-full" style={{ background: entry.color }} />
          <span className="flex-1">{entry.label}</span>
          <span className="font-medium tabular-nums">{entry.value}</span>
        </span>
      ))}
    </div>
  );
}
