import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { animalsApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { AppThemeToggle } from "@/components/controls";
import { AnimalEditor } from "@/components/editor";
import { AnimalsScreen } from "@/components/list";
import { LIST_HASH, useHashRoute, useResource } from "@/hooks";
import type { AnimalOptions, Route } from "@/types";

function RouteView({ route, options }: { route: Route; options: AnimalOptions }) {
  if (route.view === "list") return <AnimalsScreen options={options} />;
  if (route.view === "new") return <AnimalEditor key="new" options={options} animalId={null} />;
  return <AnimalEditor key={route.id} options={options} animalId={route.id} />;
}

function AppContent() {
  const route = useHashRoute();
  const options = useResource(animalsApi.options);
  if (options.data) return <RouteView route={route} options={options.data} />;
  return (
    <PageContainer size="lg">
      {options.error ? (
        <ErrorMessage message={options.error} onRetry={() => void options.reload()} />
      ) : (
        <LoadingRows label="Tierverwaltung wird geladen" />
      )}
    </PageContainer>
  );
}

export function App() {
  return (
    <AppShell
      brand="Tierverwaltung"
      homeHref={LIST_HASH}
      skipLinkLabel="Zum Inhalt springen"
      controls={<AppThemeToggle />}
    >
      <AppContent />
    </AppShell>
  );
}
