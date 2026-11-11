import {
  ALL_TYPES,
  CardBody,
  CardShell,
  ChartPanel,
  MultiLineChart,
  ViewToggle,
  slotColor,
  useChartPrefs,
  useView,
} from "@tsvd/tool-controls";
import type { Widgets } from "@/types";
import { fillDays, formatCompact, formatDay } from "@tsvd/tool-controls";
import { ChartSettings } from "@tsvd/tool-controls";

const SERIES = [
  { key: "accepted", label: "Akzeptiert", color: slotColor(1) },
  { key: "declined", label: "Abgelehnt", color: slotColor(2) },
  { key: "customized", label: "Angepasst", color: slotColor(3) },
] as const;

const VIEWS = [
  { value: "all", label: "Alle" },
  ...SERIES.map(({ key, label }) => ({ value: key, label })),
];

export function ConsentCard({
  consent,
  range,
  className,
}: {
  consent: Widgets["consent"];
  range: number;
  className?: string;
}) {
  const [view, setView] = useView("consent", VIEWS);
  const { prefs, setGrid, setColored } = useChartPrefs("consent-all", ["line"], "line");
  const days = fillDays(consent.accepted, range).map((row) => row.label);
  const byDay = new Map(consent.combined.map((row) => [row.label, row]));
  const active = SERIES.find((entry) => entry.key === view);
  return (
    <CardShell
      title="Consent-Entscheidungen"
      description="Antworten auf den Cookie-Hinweis pro Tag"
      className={className}
      views={<ViewToggle label="Ansicht Consent" options={VIEWS} value={view} onChange={setView} />}
    >
      {active ? (
        <ChartPanel
          key={active.key}
          spec={{
            id: `consent-${active.key}`,
            rows: fillDays(consent[active.key], range),
            types: ALL_TYPES,
            fallback: "line",
            color: active.color,
            label: active.label,
            format: formatCompact,
            rowLabel: formatDay,
          }}
        />
      ) : (
        <CardBody
          footer={
            <ChartSettings id="consent-all" prefs={prefs} onGrid={setGrid} onColored={setColored} />
          }
        >
          <MultiLineChart
            labels={days.map(formatDay)}
            series={SERIES.map((entry) => ({
              key: entry.key,
              label: entry.label,
              color: entry.color,
              values: days.map((day) => byDay.get(day)?.[entry.key] ?? 0),
            }))}
            format={formatCompact}
            grid={prefs.grid}
            label="Consent-Entscheidungen pro Tag"
          />
        </CardBody>
      )}
    </CardShell>
  );
}
