import { PageContainer } from "@web-app-kit/frontend/ui";
import { BASICS_PREVIEW_IDS } from "../preview/preview-ids";
import { PreviewPair } from "../preview/preview-pair";
import { PageHeader } from "../shared/page-header";

export function RadiiPage() {
  return (
    <PageContainer size="lg" className="flex flex-col gap-10">
      <PageHeader title="Radien">
        Alle Radien leiten sich aus --radius (0,625rem) ab: rounded-lg entspricht --radius, die
        übrigen Stufen sind Vielfache davon.
      </PageHeader>
      <PreviewPair previewId={BASICS_PREVIEW_IDS.radii} title="Radien" />
    </PageContainer>
  );
}
