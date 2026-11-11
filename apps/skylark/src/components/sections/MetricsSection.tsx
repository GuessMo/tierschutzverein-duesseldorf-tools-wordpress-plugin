import { HALF } from "@tsvd/tool-controls";
import { MetricCard } from "@tsvd/tool-controls";
import { slotColor } from "@tsvd/tool-controls";
import {
  fillDays,
  formatCompact,
  formatDay,
  formatDuration,
  formatNumber,
  minuteTicks,
  scoreTicks,
} from "@tsvd/tool-controls";
import type { SectionProps } from "./sections.types";

const formatScore = (value: number) => `${formatNumber(value)} / 100`;

export function MetricsSection({ data }: SectionProps) {
  const { series, compare, range } = data;
  const shared = { rowLabel: formatDay, className: HALF };
  return (
    <>
      <MetricCard
        {...shared}
        id="pageviews"
        title="Seitenaufrufe über Zeit"
        label="Seitenaufrufe"
        rows={fillDays(series.pageviews, range)}
        pairs={compare.pageviews}
        color={slotColor(1)}
        format={formatCompact}
      />
      <MetricCard
        {...shared}
        id="visitors"
        title="Besucher über Zeit"
        label="Besucher"
        rows={fillDays(series.visitors, range)}
        pairs={compare.visitors}
        color={slotColor(2)}
        format={formatCompact}
      />
      <MetricCard
        {...shared}
        id="duration"
        title="Besuchsdauer über Zeit"
        description="Durchschnitt pro Tag, Minuten:Sekunden"
        label="Besuchsdauer"
        rows={fillDays(series.duration, range)}
        pairs={compare.duration}
        color={slotColor(3)}
        format={formatDuration}
        ticks={minuteTicks}
      />
      <MetricCard
        {...shared}
        id="seo-score"
        title="SEO-Score über Zeit"
        description="Durchschnitt aller Texte, Tage mit Messung"
        label="SEO-Score"
        rows={series.seoScore}
        pairs={compare.seoScore}
        color={slotColor(7)}
        format={formatScore}
        axisFormat={formatNumber}
        ticks={scoreTicks}
      />
    </>
  );
}
