import type { SeoListProps } from "./sections.types";

const LINK_CLASSES = [
  "min-w-0 truncate rounded-sm hover:underline focus-visible:outline-2",
  "focus-visible:outline-ring active:text-primary",
].join(" ");

function deltaLabel(delta: number): string {
  return delta > 0 ? `+${delta}` : String(delta);
}

export function SeoList({ rows, showDelta = false }: SeoListProps) {
  if (rows.length === 0) return <p className="text-sm text-muted-foreground">Noch keine Daten.</p>;
  return (
    <ol className="flex flex-col divide-y">
      {rows.map((row) => (
        <li key={row.url} className="flex items-baseline justify-between gap-4 py-2 text-sm">
          <a href={row.url} target="_blank" rel="noreferrer" className={LINK_CLASSES}>
            {row.title}
          </a>
          <span className="shrink-0 tabular-nums text-muted-foreground">
            {row.score} / 100
            {showDelta && row.delta !== 0 && ` (${deltaLabel(row.delta)})`}
          </span>
        </li>
      ))}
    </ol>
  );
}
