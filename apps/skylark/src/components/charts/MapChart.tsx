import { useState } from "react";
import type { CountRow } from "@/types";
import { BarList } from "./BarList";
import { countryName } from "./countryName";
import { EmptyNote } from "./EmptyNote";
import { useWorldShapes } from "./useWorldShapes";

interface MapChartProps {
  rows: CountRow[];
  url: string;
  color: string;
  format: (value: number) => string;
  label: string;
}

const EMPTY_FILL = "color-mix(in oklab, var(--foreground) 12%, var(--card))";

function shade(value: number, max: number, color: string): string {
  if (value <= 0) return EMPTY_FILL;
  const strength = Math.round(35 + (value / max) * 65);
  return `color-mix(in oklab, ${color} ${strength}%, var(--card))`;
}

export function MapChart({ rows, url, color, format, label }: MapChartProps) {
  const { shapes, failed } = useWorldShapes(url);
  const [hover, setHover] = useState<string | null>(null);
  const totals = new Map(rows.map((row) => [row.label.toUpperCase(), row.total]));
  const max = Math.max(...rows.map((row) => row.total), 1);
  const hovered = hover
    ? `${countryName(hover)}: ${format(totals.get(hover) ?? 0)}`
    : "Land zeigen für Werte";
  return (
    <div className="flex flex-col gap-4">
      {failed && <EmptyNote text="Die Weltkarte konnte nicht geladen werden." />}
      {shapes && (
        <figure className="flex flex-col gap-2">
          <svg
            viewBox="0 0 1000 500"
            role="img"
            aria-label={label}
            className="chart-fade mx-auto h-auto max-h-[28rem] w-full"
          >
            {shapes.map((shape) => (
              <path
                key={shape.id}
                d={shape.d}
                className="stroke-background transition-opacity"
                strokeWidth={0.6}
                style={{
                  fill: shade(totals.get(shape.id) ?? 0, max, color),
                  opacity: hover && hover !== shape.id ? 0.7 : 1,
                }}
                onPointerEnter={() => setHover(shape.id)}
                onPointerLeave={() => setHover(null)}
              />
            ))}
          </svg>
          <figcaption aria-live="polite" className="text-xs text-muted-foreground">
            {hovered}
          </figcaption>
        </figure>
      )}
      {rows.length > 0 && (
        <BarList
          rows={rows}
          label={label}
          color={color}
          colored={false}
          grid
          format={format}
          rowLabel={countryName}
        />
      )}
    </div>
  );
}
