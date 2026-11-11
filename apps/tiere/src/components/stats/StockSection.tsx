import { formatNumber, monthLabel, StatTile, WIDE_XL } from "@tsvd/tool-controls";
import type { StockStats } from "@/types";
import { statsChart } from "./statsCharts";
import { StatsCard } from "./StatsCard";
import { StatsSection } from "./StatsSection";

const HINT =
  "Momentaufnahme veröffentlichter Tiere nach Vermittlungsstatus, unabhängig von den Anfragen.";

export function StockSection({ stock }: { stock: StockStats }) {
  const kpis = stock.byStatus.map((row) => (
    <StatTile key={row.label} label={row.label} value={formatNumber(row.total)} />
  ));
  const adopted = statsChart("adopted", "Vermittelt", stock.adopted, 3, "line");
  return (
    <StatsSection title="Tierbestand" hint={HINT} kpis={kpis}>
      <StatsCard
        title="Vermittelt pro Monat"
        spec={{ ...adopted, rowLabel: monthLabel }}
        className={WIDE_XL}
      />
      <StatsCard
        title="Aufmerksam geworden durch"
        spec={statsChart("sources", "Tiere", stock.sources, 5, "donut")}
      />
    </StatsSection>
  );
}
