import { useStoredState } from "../hooks";
import type { ChartPrefs, ChartType } from "./chartTypes";

export function useChartPrefs(id: string, types: ChartType[], fallback: ChartType) {
  const [prefs, setPrefs] = useStoredState<ChartPrefs>(`chart:${id}`, {
    type: fallback,
    grid: true,
    colored: null,
  });
  const safe = types.includes(prefs.type) ? prefs : { ...prefs, type: fallback };
  return {
    prefs: safe,
    setType: (type: ChartType) => setPrefs({ ...safe, type }),
    setGrid: (grid: boolean) => setPrefs({ ...safe, grid }),
    setColored: (colored: boolean) => setPrefs({ ...safe, colored }),
  };
}
