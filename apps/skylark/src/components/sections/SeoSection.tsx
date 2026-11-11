import { Tabs, TabsContent, TabsList, TabsTrigger } from "@web-app-kit/frontend/ui";
import { ChartCard } from "@/components/charts";
import type { SeoLists } from "@/types";
import { SeoList } from "./SeoList";
import type { SectionProps } from "./sections.types";

const TABS: { value: keyof SeoLists; label: string }[] = [
  { value: "worst", label: "Schlechteste" },
  { value: "best", label: "Beste" },
  { value: "below", label: "Unter 90" },
  { value: "deltas", label: "Veränderung" },
];

export function SeoSection({ data }: SectionProps) {
  return (
    <ChartCard title="Texte nach SEO-Score" description="Veröffentlichte Beiträge und Seiten">
      <Tabs defaultValue="worst" className="gap-4">
        <TabsList variant="line" aria-label="Auswahl der Texte">
          {TABS.map((tab) => (
            <TabsTrigger key={tab.value} value={tab.value}>
              {tab.label}
            </TabsTrigger>
          ))}
        </TabsList>
        {TABS.map((tab) => (
          <TabsContent key={tab.value} value={tab.value}>
            <SeoList rows={data.seo[tab.value]} showDelta={tab.value === "deltas"} />
          </TabsContent>
        ))}
      </Tabs>
    </ChartCard>
  );
}
