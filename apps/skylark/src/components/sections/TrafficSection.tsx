import { ChartCard, LineChart } from "@/components/charts";
import {
  fillDays,
  formatCompact,
  formatDuration,
  formatNumber,
  minuteTicks,
  scoreTicks,
} from "@/utils";
import type { SectionProps } from "./sections.types";

const formatScore = (value: number) => `${formatNumber(value)} / 100`;

export function TrafficSection({ data }: SectionProps) {
  const { series, range } = data;
  return (
    <section aria-label="Verlauf" className="grid gap-4 lg:grid-cols-2">
      <ChartCard title="Seitenaufrufe pro Tag">
        <LineChart
          rows={fillDays(series.pageviews, range)}
          label="Seitenaufrufe pro Tag"
          format={formatCompact}
          series={1}
        />
      </ChartCard>
      <ChartCard title="Besucher pro Tag">
        <LineChart
          rows={fillDays(series.visitors, range)}
          label="Besucher pro Tag"
          format={formatCompact}
          series={2}
        />
      </ChartCard>
      <ChartCard title="Ø Besuchsdauer pro Tag" description="Minuten:Sekunden">
        <LineChart
          rows={fillDays(series.duration, range)}
          label="Durchschnittliche Besuchsdauer"
          format={formatDuration}
          ticks={minuteTicks}
          series={3}
        />
      </ChartCard>
      <ChartCard title="SEO-Score" description="Durchschnitt aller Texte, Tage mit Messung">
        <LineChart
          rows={series.seoScore}
          label="SEO-Score"
          format={formatScore}
          axisFormat={formatNumber}
          ticks={scoreTicks}
          series={7}
        />
      </ChartCard>
    </section>
  );
}
