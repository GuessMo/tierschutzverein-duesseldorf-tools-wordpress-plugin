import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { AppThemeToggle } from "@/components/controls";
import { readBootstrap } from "@/config/bootstrap";
import { ToolTile } from "./ToolTile";

export function StartScreen() {
  const { tools, userName } = readBootstrap();
  const firstName = userName.split(" ")[0];

  return (
    <AppShell brand="Tierschutzverein" skipLinkLabel="Zum Inhalt springen" controls={<AppThemeToggle />}>
      <PageContainer size="md" className="flex flex-col gap-6">
        <h1 className="text-xl font-semibold">Hallo {firstName}, womit möchtest du arbeiten?</h1>
        <ul className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
          {tools.map((tool) => (
            <li key={tool.slug}>
              <ToolTile tool={tool} />
            </li>
          ))}
        </ul>
      </PageContainer>
    </AppShell>
  );
}
