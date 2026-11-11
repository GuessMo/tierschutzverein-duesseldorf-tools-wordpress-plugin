import {
  CardBody,
  CardShell,
  GroupedBarList,
  monthLabel,
  slotColor,
  StatTile,
  HALF,
} from "@tsvd/tool-controls";
import type { ResponseTimes } from "@/types";
import { statsChart } from "./statsCharts";
import { StatsCard } from "./StatsCard";
import { StatsSection } from "./StatsSection";

const HINT =
  "Zeit zwischen Nachrichten in den Anfragen. Interne Notizen, Spam, Blockierte und " +
  "Papierkorb zählen nicht. Median = typischer Wert, Ø = Durchschnitt.";

const formatHours = (value: number) => `${value} Std.`;

export function TimesSection({ times }: { times: ResponseTimes }) {
  const kpis = times.kpis.map((kpi) => (
    <StatTile key={kpi.label} label={kpi.label} value={kpi.value} sub={kpi.sub} />
  ));
  const first = {
    ...statsChart("first-reply", "Stunden", times.first, 1, "line"),
    format: formatHours,
  };
  return (
    <StatsSection title="Antwortzeiten" hint={HINT} kpis={kpis}>
      <StatsCard
        title="Erste Antwort pro Monat"
        description="Median in Stunden"
        spec={{ ...first, rowLabel: monthLabel }}
        className={HALF}
      />
      <CardShell
        title="Verein und Interessierte"
        description="Median in Stunden je Monat"
        className={HALF}
      >
        <CardBody>
          <GroupedBarList
            rows={times.compare.map((row) => ({
              label: row.label,
              values: [row.verein, row.person],
            }))}
            series={[
              { label: "Verein", color: slotColor(1) },
              { label: "Interessierte", color: slotColor(2) },
            ]}
            label="Antwortzeit Verein und Interessierte"
            format={formatHours}
            rowLabel={monthLabel}
          />
        </CardBody>
      </CardShell>
    </StatsSection>
  );
}
