import { ALL_TYPES } from "@/components/charts";
import type { RedirectRoute } from "@/types";
import { fillDays, formatCompact, formatDay, formatNumber } from "@/utils";
import { AllUrlsDialog } from "./AllUrlsDialog";
import { CardBody, CardShell } from "./CardShell";
import { ChartPanel } from "./ChartPanel";
import { useCompareView } from "./CompareView";
import { RedirectCounts } from "./RedirectCounts";
import { TopList } from "./TopList";
import { useView } from "./useView";
import { ViewToggle } from "./ViewToggle";

const VIEWS = [
  { value: "time", label: "Über Zeit" },
  { value: "counts", label: "Anzahl" },
  { value: "top", label: "Top" },
  { value: "compare", label: "Vergleich" },
];

interface RedirectCardProps {
  route: RedirectRoute;
  range: number;
  color: string;
}

export function RedirectCard({ route, range, color }: RedirectCardProps) {
  const id = `redirect-${route.code}`;
  const [view, setView] = useView(id, VIEWS);
  const compare = useCompareView({ id, pairs: route.compare, color, format: formatNumber });
  const toggle = (
    <ViewToggle label={`Ansicht ${route.label}`} options={VIEWS} value={view} onChange={setView} />
  );
  return (
    <CardShell
      title={route.label}
      description={`${formatNumber(route.total)} Aufrufe über ?route=${route.code}`}
      views={toggle}
    >
      {view === "time" && (
        <ChartPanel
          spec={{
            id: `${id}-time`,
            rows: fillDays(route.series, range),
            types: ALL_TYPES,
            fallback: "line",
            color,
            label: `${route.label} pro Tag`,
            format: formatCompact,
            rowLabel: formatDay,
          }}
        />
      )}
      {view === "counts" && (
        <CardBody>
          <RedirectCounts id={id} counts={route.counts} color={color} />
        </CardBody>
      )}
      {view === "top" && (
        <CardBody footer={<AllUrlsDialog title={route.label} rows={route.all} />}>
          <TopList rows={route.top} />
        </CardBody>
      )}
      {view === "compare" && <CardBody footer={compare.footer}>{compare.body}</CardBody>}
    </CardShell>
  );
}
