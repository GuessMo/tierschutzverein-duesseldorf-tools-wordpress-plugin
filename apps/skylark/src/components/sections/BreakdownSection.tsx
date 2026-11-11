import { BarList, ChartCard } from "@/components/charts";
import type { SeriesSlot } from "@/components/charts";
import type { Breakdowns } from "@/types";
import type { SectionProps } from "./sections.types";

interface BreakdownSpec {
  key: keyof Breakdowns;
  title: string;
  series: SeriesSlot;
  description?: string;
}

const SPECS: BreakdownSpec[] = [
  { key: "pages", title: "Meistbesuchte Seiten", series: 1 },
  { key: "referrers", title: "Herkunft", series: 2, description: "Verweisende Websites" },
  {
    key: "notFound",
    title: "Nicht gefunden (404)",
    series: 8,
    description: "Aufgerufene Adressen ohne Seite",
  },
  { key: "countries", title: "Länder", series: 6 },
  { key: "devices", title: "Geräte", series: 5 },
  { key: "browsers", title: "Browser", series: 4 },
  { key: "systems", title: "Betriebssysteme", series: 7 },
];

export function BreakdownSection({ data }: SectionProps) {
  return (
    <section aria-label="Aufschlüsselung" className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
      {SPECS.map((spec) => (
        <ChartCard key={spec.key} title={spec.title} description={spec.description}>
          <BarList rows={data.breakdowns[spec.key]} label={spec.title} series={spec.series} />
        </ChartCard>
      ))}
    </section>
  );
}
