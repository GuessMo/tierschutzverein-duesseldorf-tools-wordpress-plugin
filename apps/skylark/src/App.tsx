import { useCallback } from "react";
import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { analyticsApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { AppControls } from "@/components/controls";
import { Dashboard } from "@/components/Dashboard";
import { RangeToggle } from "@/components/sections";
import { useAutoRefresh, useResource, useStoredState } from "@/hooks";
import type { AnalyticsRange } from "@/types";

export function App() {
  const [range, setRange] = useStoredState<AnalyticsRange>("range", 30);
  const load = useCallback(() => analyticsApi.data(range), [range]);
  const analytics = useResource(load);
  const reload = useCallback(() => void analytics.reload(), [analytics.reload]);
  useAutoRefresh(analytics.data?.refresh ?? 0, reload);
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
          <div className="flex items-center gap-3">
            {analytics.isLoading && analytics.data && (
              <span role="status" className="text-sm text-muted-foreground">
                Wird aktualisiert …
              </span>
            )}
            <RangeToggle value={range} onChange={setRange} />
          </div>
        </div>
        {analytics.error && <ErrorMessage message={analytics.error} onRetry={reload} />}
        {!analytics.data && !analytics.error && (
          <LoadingRows label="Statistik wird geladen" count={6} />
        )}
        {analytics.data && <Dashboard data={analytics.data} isStale={analytics.isLoading} />}
      </PageContainer>
    </AppShell>
  );
}
