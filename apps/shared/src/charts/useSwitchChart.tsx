import { ChartFooter } from "./ChartFooter";
import type { ChartType } from "./chartTypes";
import { useChartPrefs } from "./useChartPrefs";

export function useSwitchChart(id: string, types: ChartType[], fallback: ChartType) {
  const { prefs, setType, setGrid, setColored } = useChartPrefs(id, types, fallback);
  const footer = (
    <ChartFooter
      id={id}
      types={types}
      prefs={prefs}
      onType={setType}
      onGrid={setGrid}
      onColored={setColored}
    />
  );
  return { prefs, footer };
}
