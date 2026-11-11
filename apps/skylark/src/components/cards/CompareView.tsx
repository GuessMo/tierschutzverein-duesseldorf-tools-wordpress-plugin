import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";
import {
  ChartSettings,
  CompareIcon,
  EmptyNote,
  MultiLineChart,
  NEUTRAL,
  useChartPrefs,
} from "@/components/charts";
import { useStoredState } from "@/hooks";
import type { ComparePair } from "@/types";
import type { TickScale } from "@/utils";
import { monthLabel } from "./monthLabel";

interface CompareViewProps {
  id: string;
  pairs: ComparePair[];
  color: string;
  format: (value: number) => string;
  axisFormat?: (value: number) => string;
  ticks?: TickScale;
}

function deltaText(delta: number | null): string {
  if (delta === null) return "–";
  return `${delta > 0 ? "+" : ""}${delta} %`;
}

export function useCompareView({ id, pairs, color, format, axisFormat, ticks }: CompareViewProps) {
  const [key, setKey] = useStoredState(`compare:${id}`, pairs[0]?.key ?? "");
  const { prefs, setGrid, setColored } = useChartPrefs(`${id}-compare`, ["line"], "line");
  const pair = pairs.find((entry) => entry.key === key) ?? pairs[0];
  const body = !pair ? (
    <EmptyNote />
  ) : (
    <div className="flex flex-col gap-3">
      <div className="flex flex-wrap items-baseline gap-x-3">
        <span className="text-2xl font-semibold tabular-nums">{deltaText(pair.delta)}</span>
        <span className="text-sm text-muted-foreground">{pair.label}</span>
      </div>
      <MultiLineChart
        labels={pair.rows.map((row) => `${row.day}.`)}
        series={[
          {
            key: "a",
            label: monthLabel(pair.aMonth),
            color,
            values: pair.rows.map((row) => row.a),
          },
          {
            key: "b",
            label: monthLabel(pair.bMonth),
            color: NEUTRAL,
            values: pair.rows.map((row) => row.b),
          },
        ]}
        format={format}
        axisFormat={axisFormat}
        ticks={ticks}
        grid={prefs.grid}
        label={pair.label}
      />
    </div>
  );
  const footer = (
    <div className="flex w-full items-center justify-between gap-2">
      <ToggleGroup
        type="single"
        variant="outline"
        size="sm"
        label="Vergleich"
        value={pair?.key ?? ""}
        onValueChange={(next) => next && setKey(next)}
      >
        {pairs.map((entry) => (
          <ToggleGroupItem
            key={entry.key}
            value={entry.key}
            aria-label={entry.label}
            title={entry.label}
          >
            <CompareIcon gap={entry.gap} />
          </ToggleGroupItem>
        ))}
      </ToggleGroup>
      <ChartSettings id={`${id}-compare`} prefs={prefs} onGrid={setGrid} onColored={setColored} />
    </div>
  );
  return { body, footer };
}
