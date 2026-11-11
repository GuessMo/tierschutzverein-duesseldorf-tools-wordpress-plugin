export type ChartType = "vbars" | "hbars" | "line" | "donut" | "pie" | "map";

export interface ChartPrefs {
  type: ChartType;
  grid: boolean;
  colored: boolean | null;
}

export const ALL_TYPES: ChartType[] = ["vbars", "hbars", "line", "donut", "pie"];
export const GEO_TYPES: ChartType[] = ["map", "hbars", "vbars", "donut", "pie"];

export const TYPE_LABELS: Record<ChartType, string> = {
  vbars: "Säulen",
  hbars: "Balken",
  line: "Linie",
  donut: "Ring",
  pie: "Torte",
  map: "Karte",
};

export function supportsGrid(type: ChartType): boolean {
  return type === "line" || type === "vbars" || type === "hbars";
}

export function supportsColor(type: ChartType): boolean {
  return type === "vbars" || type === "hbars" || type === "donut" || type === "pie";
}

export function isColored(prefs: ChartPrefs): boolean {
  if (!supportsColor(prefs.type)) return false;
  return prefs.colored ?? (prefs.type === "donut" || prefs.type === "pie");
}
