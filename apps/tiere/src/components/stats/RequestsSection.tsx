import {
  CardBody,
  CardShell,
  formatNumber,
  GroupedBarList,
  monthLabel,
  slotColor,
  StatTile,
  WIDE,
} from "@tsvd/tool-controls";
import type { RequestStats } from "@/types";
import { statsChart } from "./statsCharts";
import { StatsCard } from "./StatsCard";
import { StatsSection } from "./StatsSection";

const HINT =
  "Zählt eingegangene Formular-Anfragen (Interesse, Vermisst-Meldung, Antrag auf " +
  "Privatvermittlung), nicht den aktuellen Tierbestand.";

function trendText(requests: RequestStats): string {
  const { average, delta } = requests.trend;
  const base = `Ø 3 Vormonate: ${formatNumber(average)}`;
  if (delta === null) return base;
  return `${base} · ${delta >= 0 ? "+" : ""}${delta} %`;
}

function Kpis({ requests }: { requests: RequestStats }) {
  return (
    <>
      <StatTile label="Anfragen gesamt" value={formatNumber(requests.total)} />
      <StatTile
        label="Anfragen diesen Monat"
        value={formatNumber(requests.trend.thisMonth)}
        sub={trendText(requests)}
      />
      <StatTile label="davon Interesse" value={formatNumber(requests.interest)} />
      <StatTile label="davon Privatvermittlung" value={formatNumber(requests.private)} />
      <StatTile label="davon Vermisst-Meldungen" value={formatNumber(requests.missing)} />
    </>
  );
}

function ListedVsCard({ requests }: { requests: RequestStats }) {
  return (
    <CardShell title="Gelistet und Anfragen je Tierart" className={WIDE}>
      <CardBody>
        <GroupedBarList
          rows={requests.listedVs.map((row) => ({
            label: row.label,
            values: [row.listed, row.requests],
          }))}
          series={[
            { label: "Gelistet", color: slotColor(1) },
            { label: "Anfragen", color: slotColor(2) },
          ]}
          label="Gelistet und Anfragen je Tierart"
          format={formatNumber}
        />
      </CardBody>
    </CardShell>
  );
}

export function RequestsSection({ requests }: { requests: RequestStats }) {
  const months = { ...statsChart("months", "Anfragen", requests.months, 1, "line") };
  return (
    <StatsSection title="Anfragen" hint={HINT} kpis={<Kpis requests={requests} />}>
      <StatsCard
        title="Anfragen pro Monat"
        spec={{ ...months, rowLabel: monthLabel }}
        className={WIDE}
      />
      <StatsCard
        title="Anfragen nach Art"
        spec={statsChart("type", "Anfragen", requests.byType, 2, "pie")}
      />
      <StatsCard
        title="Wohnort"
        spec={statsChart("residence", "Anfragen", requests.residence, 3, "donut")}
      />
      <StatsCard
        title="Unterkunft"
        spec={statsChart("housing", "Anfragen", requests.housing, 5, "donut")}
      />
      <StatsCard
        title="Außenbereich"
        spec={statsChart("outdoor", "Anfragen", requests.outdoor, 6, "donut")}
      />
      <StatsCard
        title="Top-Tiere"
        spec={statsChart("top", "Anfragen", requests.topAnimals, 1, "hbars")}
        className={WIDE}
      />
      <StatsCard
        title="Anfragen nach Tierart"
        spec={statsChart("species", "Anfragen", requests.species, 7, "hbars")}
      />
      <ListedVsCard requests={requests} />
      <StatsCard
        title="Anfragen nach Rasse"
        spec={statsChart("breed", "Anfragen", requests.breed, 4, "hbars")}
      />
    </StatsSection>
  );
}
