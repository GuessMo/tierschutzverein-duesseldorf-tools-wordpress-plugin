import { useCallback, useState } from "react";
import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { analyticsApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { AppControls } from "@/components/controls";
import {
  BreakdownSection,
  KpiRow,
  RangeToggle,
  RedirectSection,
  SeoSection,
  TrafficSection,
} from "@/components/sections";
import { useResource } from "@/hooks";
import type { AnalyticsData, AnalyticsRange } from "@/types";

function Dashboard({ data }: { data: AnalyticsData }) {
  return (
    <>
      <KpiRow data={data} />
      <TrafficSection data={data} />
      <BreakdownSection data={data} />
      <RedirectSection data={data} />
      <SeoSection data={data} />
    </>
  );
}

export function App() {
  const [range, setRange] = useState<AnalyticsRange>(30);
  const load = useCallback(() => analyticsApi.data(range), [range]);
  const analytics = useResource(load);
  return (
    <AppShell
      brand="Skylark"
      homeHref="#"
      skipLinkLabel="Zum Inhalt springen"
      controls={<AppControls />}
    >
      <PageContainer size="full" className="flex flex-col gap-6">
        <div className="flex flex-wrap items-center justify-between gap-4">
          <h1 className="text-2xl font-semibold">Besucherstatistik</h1>
          <RangeToggle value={range} onChange={setRange} />
        </div>
        {analytics.error && (
          <ErrorMessage message={analytics.error} onRetry={() => void analytics.reload()} />
        )}
        {!analytics.data && !analytics.error && (
          <LoadingRows label="Statistik wird geladen" count={6} />
        )}
        {analytics.data && <Dashboard data={analytics.data} />}
      </PageContainer>
    </AppShell>
  );
}
