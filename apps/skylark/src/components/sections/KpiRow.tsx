import { StatTile } from "@tsvd/tool-controls";
import { formatDuration, formatNumber } from "@tsvd/tool-controls";
import type { SectionProps } from "./sections.types";

export function KpiRow({ data }: SectionProps) {
  const { totals } = data;
  return (
    <section aria-label="Kennzahlen" className="grid grid-cols-2 gap-4 lg:grid-cols-4">
      <StatTile label="Seitenaufrufe" value={formatNumber(totals.pageviews)} />
      <StatTile label="Besucher" value={formatNumber(totals.visitors)} />
      <StatTile label="Absprungrate" value={`${totals.bounceRate} %`} />
      <StatTile label="Ø Besuchsdauer" value={formatDuration(totals.avgDuration)} />
    </section>
  );
}
