import { ChartView, useSwitchChart } from "@/components/charts";
import type { ChartPanelSpec } from "./cards.types";
import { CardBody } from "./CardShell";

export function ChartPanel({ spec }: { spec: ChartPanelSpec }) {
  const { prefs, footer } = useSwitchChart(spec.id, spec.types, spec.fallback);
  return (
    <CardBody footer={footer}>
      <ChartView
        key={prefs.type}
        rows={spec.rows}
        prefs={prefs}
        color={spec.color}
        label={spec.label}
        format={spec.format}
        axisFormat={spec.axisFormat}
        ticks={spec.ticks}
        rowLabel={spec.rowLabel}
        mapUrl={spec.mapUrl}
      />
    </CardBody>
  );
}
