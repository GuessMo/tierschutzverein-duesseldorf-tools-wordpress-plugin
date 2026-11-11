import { BarList, ChartCard, LineChart } from "@/components/charts";
import { fillDays, formatCompact, formatNumber } from "@/utils";
import type { SectionProps } from "./sections.types";

export function RedirectSection({ data }: SectionProps) {
  if (data.redirects.length === 0) return null;
  return (
    <section aria-label="Weiterleitungen" className="grid gap-4 lg:grid-cols-2">
      {data.redirects.map((route) => (
        <ChartCard
          key={route.code}
          title={route.label}
          description={`${formatNumber(route.total)} Aufrufe über ?route=${route.code}`}
        >
          <div className="flex flex-col gap-6">
            <LineChart
              rows={fillDays(route.series, data.range)}
              label={`${route.label} pro Tag`}
              format={formatCompact}
              series={3}
            />
            <BarList rows={route.top} label={`${route.label}: Zielseiten`} series={3} />
          </div>
        </ChartCard>
      ))}
    </section>
  );
}
