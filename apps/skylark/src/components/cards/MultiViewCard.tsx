import { ChartPanel } from "./ChartPanel";
import type { MultiViewCardProps, ViewPanel } from "./cards.types";
import { CardBody, CardShell } from "./CardShell";
import { TopList } from "./TopList";
import { useView } from "./useView";
import { ViewToggle } from "./ViewToggle";

function Panel({ panel }: { panel: ViewPanel }) {
  if (panel.kind === "chart") return <ChartPanel spec={panel.chart} />;
  return (
    <CardBody>
      <TopList rows={panel.rows} empty={panel.empty} showTotals={panel.showTotals} />
    </CardBody>
  );
}

export function MultiViewCard({ id, title, description, className, panels }: MultiViewCardProps) {
  const [view, setView] = useView(id, panels);
  const active = panels.find((panel) => panel.value === view) ?? panels[0];
  const toggle =
    panels.length > 1 ? (
      <ViewToggle label={`Ansicht ${title}`} options={panels} value={view} onChange={setView} />
    ) : undefined;
  return (
    <CardShell title={title} description={description} views={toggle} className={className}>
      {active && <Panel key={active.value} panel={active} />}
    </CardShell>
  );
}
