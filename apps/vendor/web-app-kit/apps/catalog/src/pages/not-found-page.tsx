import { PageContainer } from "@web-app-kit/frontend/ui";
import { PATHS } from "../routing/paths";
import { PageHeader } from "../shared/page-header";
import { TextLink } from "../shared/text-link";

export function NotFoundPage() {
  return (
    <PageContainer size="md" className="flex flex-col gap-4">
      <PageHeader title="Seite nicht gefunden">Diese Adresse gibt es im Katalog nicht.</PageHeader>
      <TextLink href={PATHS.components}>Zu allen Komponenten</TextLink>
    </PageContainer>
  );
}
