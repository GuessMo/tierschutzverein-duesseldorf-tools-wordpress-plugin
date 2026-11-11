import type { CountRow } from "../types";

const MAX_SLICES = 8;

export function foldRows(rows: CountRow[]): CountRow[] {
  if (rows.length <= MAX_SLICES) return rows;
  const head = rows.slice(0, MAX_SLICES - 1);
  const rest = rows.slice(MAX_SLICES - 1).reduce((sum, row) => sum + row.total, 0);
  return [...head, { label: "Andere", total: rest }];
}

function point(center: number, radius: number, angle: number): string {
  return `${center + radius * Math.sin(angle)},${center - radius * Math.cos(angle)}`;
}

export function arcPath(
  center: number,
  outer: number,
  inner: number,
  start: number,
  end: number,
): string {
  const sweep = Math.min(end - start, Math.PI * 2 - 0.0001);
  const stop = start + sweep;
  const large = sweep > Math.PI ? 1 : 0;
  const outerEnd = point(center, outer, stop);
  const outerArc = `M${point(center, outer, start)}A${outer},${outer} 0 ${large} 1 ${outerEnd}`;
  if (inner <= 0) return `${outerArc}L${center},${center}Z`;
  const innerArc = `A${inner},${inner} 0 ${large} 0 ${point(center, inner, start)}`;
  return `${outerArc}L${point(center, inner, stop)}${innerArc}Z`;
}
