import { CardBody, CardShell, useView, ViewToggle } from "@/components/cards";
import type { SeoLists } from "@/types";
import { SeoList } from "./SeoList";

const VIEWS: { value: keyof SeoLists; label: string }[] = [
  { value: "worst", label: "Schlechteste" },
  { value: "best", label: "Beste" },
  { value: "below", label: "Unter 90" },
  { value: "deltas", label: "Veränderung" },
];

export function SeoCard({ seo, className }: { seo: SeoLists; className?: string }) {
  const [view, setView] = useView("seo-board", VIEWS);
  const key = view as keyof SeoLists;
  return (
    <CardShell
      title="Beste und schlechteste Texte"
      description="Nach SEO-Score, veröffentlichte Beiträge und Seiten"
      className={className}
      views={
        <ViewToggle label="Ansicht SEO-Texte" options={VIEWS} value={view} onChange={setView} />
      }
    >
      <CardBody>
        <SeoList key={key} rows={seo[key]} showDelta={key === "deltas"} />
      </CardBody>
    </CardShell>
  );
}
