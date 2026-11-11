import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { AppControls } from "@/components/controls";
import type { StartupScreenProps } from "./workspace.types";

export function StartupScreen({ error, onRetry }: StartupScreenProps) {
  return (
    <AppShell brand="Anfragen" skipLinkLabel="Zum Inhalt springen" controls={<AppControls />}>
      <PageContainer size="md">
        {error ? (
          <ErrorMessage message={error} onRetry={() => void onRetry()} />
        ) : (
          <LoadingRows label="Anfragen werden geladen" count={6} />
        )}
      </PageContainer>
    </AppShell>
  );
}
