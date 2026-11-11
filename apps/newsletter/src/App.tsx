import { AppShell } from "@web-app-kit/frontend/ui";
import { DynamicBlocksScreen } from "@/components/blocks";
import { AppControls, AreaNav } from "@/components/controls";
import { IssueEditor } from "@/components/editor";
import { IssuesScreen } from "@/components/issues";
import { SubscribersScreen } from "@/components/subscribers";
import { ISSUES_HASH, useNewsletterRoute } from "@/hooks";
import type { NewsletterRoute } from "@/types";

function RouteView({ route }: { route: NewsletterRoute }) {
  if (route.view === "issue") return <IssueEditor key={route.id} id={route.id} />;
  if (route.view === "subscribers") return <SubscribersScreen />;
  if (route.view === "blocks") return <DynamicBlocksScreen />;
  return <IssuesScreen />;
}

export function App() {
  const route = useNewsletterRoute();
  return (
    <AppShell
      brand="Newsletter"
      homeHref={ISSUES_HASH}
      skipLinkLabel="Zum Inhalt springen"
      actions={<AreaNav route={route} />}
      controls={<AppControls />}
    >
      <RouteView route={route} />
    </AppShell>
  );
}
