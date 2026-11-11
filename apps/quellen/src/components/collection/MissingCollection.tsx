import { AppShell, Button, PageContainer } from "@web-app-kit/frontend/ui";
import { StateMessage } from "@/components/common";
import { AppControls } from "@/components/controls";

export function MissingCollection({ message }: { message: string }) {
  return (
    <AppShell
      brand="Quellen"
      homeHref="#"
      skipLinkLabel="Zum Inhalt springen"
      controls={<AppControls />}
    >
      <PageContainer size="md">
        <StateMessage
          title="Sammlung nicht verfügbar"
          description={message}
          action={
            <Button asChild variant="outline">
              <a href="#">Zu den Sammlungen</a>
            </Button>
          }
        />
      </PageContainer>
    </AppShell>
  );
}
