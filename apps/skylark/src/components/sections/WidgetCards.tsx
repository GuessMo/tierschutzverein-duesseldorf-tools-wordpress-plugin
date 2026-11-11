import { WIDE } from "@/components/layout";
import { ConsentCard, MultiViewCard, RedirectCard } from "@/components/cards";
import { ALL_TYPES, slotColor } from "@/components/charts";
import { formatNumber } from "@/utils";
import type { SectionProps } from "./sections.types";

const NEVER = "Alle wurden mindestens einmal aufgerufen.";

export function WidgetCards({ data }: SectionProps) {
  const { widgets, redirects, range } = data;
  const base = { types: ALL_TYPES, format: formatNumber };
  return (
    <>
      <MultiViewCard
        id="animals"
        title="Tiere: Art und Status"
        description="Aufrufe von Tierseiten"
        className={WIDE}
        panels={[
          {
            value: "species",
            label: "Art",
            kind: "chart",
            chart: {
              ...base,
              id: "animals-species",
              label: "Aufrufe",
              rows: widgets.animals.species,
              color: slotColor(1),
              fallback: "hbars",
            },
          },
          {
            value: "status",
            label: "Status",
            kind: "chart",
            chart: {
              ...base,
              id: "animals-status",
              label: "Aufrufe",
              rows: widgets.animals.status,
              color: slotColor(2),
              fallback: "donut",
            },
          },
        ]}
      />
      {redirects.map((route) => (
        <RedirectCard key={route.code} route={route} range={range} color={slotColor(3)} />
      ))}
      <ConsentCard consent={widgets.consent} range={range} className={WIDE} />
      <MultiViewCard
        id="letters"
        title="Tierschutzbriefe"
        panels={[
          { value: "top", label: "Meistgelesen", kind: "list", rows: widgets.letters.top },
          {
            value: "never",
            label: "Nie gelesen",
            kind: "list",
            rows: widgets.letters.never,
            empty: NEVER,
            showTotals: false,
          },
        ]}
      />
      <MultiViewCard
        id="news"
        title="News"
        className={WIDE}
        panels={[
          { value: "top", label: "Meistgelesen", kind: "list", rows: widgets.news.top },
          {
            value: "categories",
            label: "Nach Kategorie",
            kind: "chart",
            chart: {
              ...base,
              id: "news-categories",
              label: "Aufrufe",
              rows: widgets.news.categories,
              color: slotColor(5),
              fallback: "hbars",
            },
          },
        ]}
      />
      <MultiViewCard
        id="projects"
        title="Meistgesehene Projekte"
        panels={[{ value: "top", label: "Top", kind: "list", rows: widgets.projects }]}
      />
      <MultiViewCard
        id="material"
        title="Informationsmaterial"
        className={WIDE}
        panels={[
          { value: "views", label: "Meistgesehen", kind: "list", rows: widgets.material.views },
          {
            value: "downloads",
            label: "Meistheruntergeladen",
            kind: "list",
            rows: widgets.material.downloads,
          },
          {
            value: "never",
            label: "Nie heruntergeladen",
            kind: "list",
            rows: widgets.material.never,
            empty: NEVER,
            showTotals: false,
          },
        ]}
      />
    </>
  );
}
