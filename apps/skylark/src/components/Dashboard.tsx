import { KpiRow, MetricsSection, SeoCard, TrafficCards, WidgetCards } from "@/components/sections";
import type { AnalyticsData } from "@/types";
import { FULL } from "@tsvd/tool-controls";

const STALE_CLASSES = [
  "flex flex-col gap-6 transition-opacity duration-300",
  "aria-busy:opacity-60 motion-reduce:transition-none",
].join(" ");

interface DashboardProps {
  data: AnalyticsData;
  isStale: boolean;
}

export function Dashboard({ data, isStale }: DashboardProps) {
  return (
    <div key={data.range} aria-busy={isStale} className={STALE_CLASSES}>
      <KpiRow data={data} />
      <div className="grid grid-flow-row-dense gap-4 md:grid-cols-2 xl:grid-cols-6 [&>*]:min-w-0">
        <MetricsSection data={data} />
        <SeoCard seo={data.seo} className={FULL} />
        <TrafficCards data={data} />
        <WidgetCards data={data} />
      </div>
    </div>
  );
}
