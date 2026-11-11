import { WIDE } from "@tsvd/tool-controls";
import { MultiViewCard, type ChartPanelSpec } from "@tsvd/tool-controls";
import { ALL_TYPES, GEO_TYPES, slotColor, type ChartType } from "@tsvd/tool-controls";
import type { CountRow } from "@/types";
import { formatNumber } from "@tsvd/tool-controls";
import type { SectionProps } from "./sections.types";

function chart(
  id: string,
  label: string,
  rows: CountRow[],
  slot: number,
  fallback: ChartType,
): ChartPanelSpec {
  const color = slotColor(slot as 1);
  return { id, label, rows, color, fallback, types: ALL_TYPES, format: formatNumber };
}

function single(
  id: string,
  title: string,
  spec: ChartPanelSpec,
  className = "",
  description?: string,
) {
  const panels = [{ value: id, label: title, kind: "chart" as const, chart: spec }];
  return (
    <MultiViewCard
      key={id}
      id={id}
      title={title}
      description={description}
      className={className}
      panels={panels}
    />
  );
}

export function TrafficCards({ data }: SectionProps) {
  const { breakdowns, mapUrl } = data;
  const countries = {
    ...chart("countries", "Länder", breakdowns.countries, 3, "map"),
    types: GEO_TYPES,
    mapUrl,
  };
  return (
    <>
      {single(
        "top-pages",
        "Meistbesuchte Seiten",
        chart("top-pages", "Seitenaufrufe", breakdowns.pages, 1, "hbars"),
        WIDE,
      )}
      {single(
        "referrers",
        "Herkunft",
        chart("referrers", "Besuche", breakdowns.referrers, 2, "hbars"),
        "",
        "Verweisende Websites",
      )}
      {single(
        "not-found",
        "Nicht gefunden (404)",
        chart("not-found", "Aufrufe", breakdowns.notFound, 8, "hbars"),
        "",
        "Adressen ohne Seite",
      )}
      {single("countries", "Länder", countries, WIDE)}
      {single("devices", "Geräte", chart("devices", "Besuche", breakdowns.devices, 5, "donut"))}
      {single("browsers", "Browser", chart("browsers", "Besuche", breakdowns.browsers, 4, "donut"))}
      {single(
        "systems",
        "Betriebssysteme",
        chart("systems", "Besuche", breakdowns.systems, 7, "donut"),
      )}
    </>
  );
}
