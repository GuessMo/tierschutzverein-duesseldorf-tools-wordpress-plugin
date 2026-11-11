import { PageContainer } from "@web-app-kit/frontend/ui";
import { BASICS_PREVIEW_IDS } from "../preview/preview-ids";
import { PreviewPair } from "../preview/preview-pair";
import { PageHeader } from "../shared/page-header";
import { Section } from "../shared/section";

export function ColorsPage() {
  return (
    <PageContainer size="lg" className="flex flex-col gap-10">
      <PageHeader title="Farben">
        Farbrollen aus theme.css und brand.css, gerendert und gemessen: Text braucht 4,5:1,
        Bedienelemente und Fokus 3:1.
      </PageHeader>
      <Section
        id="farbrollen"
        title="Farbrollen"
        intro="Fläche mit Schrift; Primär, Sekundär und Tertiär mit Hover und Gedrückt."
      >
        <PreviewPair previewId={BASICS_PREVIEW_IDS.colorRoles} title="Farbrollen" />
      </Section>
      <Section
        id="grautoene"
        title="Grautöne"
        intro="Neutrale Töne; Farbstich aus --neutral-hue und --neutral-chroma in brand.css."
      >
        <PreviewPair previewId={BASICS_PREVIEW_IDS.neutrals} title="Grautöne" />
      </Section>
    </PageContainer>
  );
}
