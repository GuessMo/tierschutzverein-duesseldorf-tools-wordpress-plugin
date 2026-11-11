import type { ReactNode } from "react";
import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { AppControls } from "@/components/controls";
import { RelayedScreen } from "@/components/relayed";

export function RelayedShell({ nav }: { nav: ReactNode }) {
  return (
    <AppShell
      brand="Anfragen"
      skipLinkLabel="Zum Inhalt springen"
      actions={nav}
      controls={<AppControls />}
    >
      <PageContainer size="lg">
        <RelayedScreen />
      </PageContainer>
    </AppShell>
  );
}
