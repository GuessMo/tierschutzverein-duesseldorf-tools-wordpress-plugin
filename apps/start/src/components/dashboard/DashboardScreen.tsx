import { HeaderControls } from "@tsvd/tool-controls";
import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { readBootstrap } from "@/config/bootstrap";
import { WidgetSlot } from "./WidgetSlot";

const SLOT_COUNT = 6;

export function DashboardScreen() {
  const bootstrap = readBootstrap();
  const firstName = bootstrap.userName.split(" ")[0];

  return (
    <AppShell
      brand="Tierschutzverein"
      skipLinkLabel="Zum Inhalt springen"
      controls={<HeaderControls {...bootstrap} />}
    >
      <PageContainer size="lg" className="flex flex-col gap-6">
        <div className="flex flex-col gap-1">
          <h1 className="text-xl font-semibold">Dashboard</h1>
          <p className="text-muted-foreground">Hallo {firstName}, deine Tools findest du oben rechts.</p>
        </div>
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {Array.from({ length: SLOT_COUNT }, (_, index) => (
            <WidgetSlot key={index} />
          ))}
        </div>
      </PageContainer>
    </AppShell>
  );
}
