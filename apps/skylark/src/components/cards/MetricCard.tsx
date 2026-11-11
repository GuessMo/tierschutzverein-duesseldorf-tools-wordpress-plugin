import { ALL_TYPES } from "@/components/charts";
import { ChartPanel } from "./ChartPanel";
import type { MetricCardProps } from "./cards.types";
import { CardBody, CardShell } from "./CardShell";
import { useCompareView } from "./CompareView";
import { useView } from "./useView";
import { ViewToggle } from "./ViewToggle";

const VIEWS = [
  { value: "time", label: "Über Zeit" },
  { value: "compare", label: "Vergleich" },
];

export function MetricCard(props: MetricCardProps) {
  const { title, description, pairs, className, ...chart } = props;
  const [view, setView] = useView(chart.id, VIEWS);
  const compare = useCompareView({ ...chart, pairs });
  return (
    <CardShell
      title={title}
      description={description}
      className={className}
      views={
        <ViewToggle label={`Ansicht ${title}`} options={VIEWS} value={view} onChange={setView} />
      }
    >
      {view === "time" ? (
        <ChartPanel key="time" spec={{ ...chart, types: ALL_TYPES, fallback: "line" }} />
      ) : (
        <CardBody key="compare" footer={compare.footer}>
          {compare.body}
        </CardBody>
      )}
    </CardShell>
  );
}
