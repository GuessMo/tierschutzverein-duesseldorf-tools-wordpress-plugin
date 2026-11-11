import { PageContainer } from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog/catalog.types";
import { importStatement } from "../catalog/import-statement";
import { componentPreviewId } from "../preview/preview-ids";
import { PreviewPair } from "../preview/preview-pair";
import { CodeBlock } from "../shared/code-block";
import { PageHeader } from "../shared/page-header";
import { Section } from "../shared/section";
import { StatusBadge } from "../shared/status-badge";
import { StateList } from "./state-list";

export function ComponentPage({ entry }: { entry: CatalogEntry }) {
  return (
    <PageContainer size="lg" className="flex flex-col gap-10">
      <PageHeader title={entry.name} aside={<StatusBadge status={entry.status} />}>
        {entry.description}
      </PageHeader>
      <Section id="import" title="Import">
        <CodeBlock code={importStatement(entry)} label={`Import von ${entry.name}`} />
      </Section>
      <Section id="varianten" title="Varianten">
        <div className="flex flex-col gap-8">
          {entry.variants.map((variant) => (
            <section key={variant.key} aria-labelledby={`variante-${variant.key}`}>
              <div className="mb-3 flex flex-col gap-1">
                <h3 id={`variante-${variant.key}`} className="font-medium">
                  {variant.title}
                </h3>
                <p className="max-w-prose text-sm text-muted-foreground">{variant.description}</p>
              </div>
              <PreviewPair
                previewId={componentPreviewId(entry.slug, variant.key)}
                title={`${entry.name}: ${variant.title}`}
                minHeight={entry.previewMinHeight}
              />
            </section>
          ))}
        </div>
      </Section>
      <Section id="zustaende" title="Zustände">
        <StateList states={entry.states} />
      </Section>
    </PageContainer>
  );
}
