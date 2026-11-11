import { PageContainer } from "@web-app-kit/frontend/ui";
import { animalsApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { useResource } from "@/hooks";
import { RequestsSection } from "./RequestsSection";
import { StockSection } from "./StockSection";
import { TimesSection } from "./TimesSection";

export function StatsScreen() {
  const stats = useResource(animalsApi.stats);
  return (
    <PageContainer size="full" className="flex flex-col gap-10">
      <h1 className="text-xl font-semibold">Interesse und Statistik</h1>
      {stats.error && <ErrorMessage message={stats.error} onRetry={() => void stats.reload()} />}
      {!stats.data && !stats.error && <LoadingRows label="Statistik wird geladen" count={6} />}
      {stats.data && (
        <>
          <RequestsSection requests={stats.data.requests} />
          <StockSection stock={stats.data.stock} />
          {stats.data.responseTimes && <TimesSection times={stats.data.responseTimes} />}
        </>
      )}
    </PageContainer>
  );
}
