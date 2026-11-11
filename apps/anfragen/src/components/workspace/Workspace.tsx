import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { CandidatesDialog } from "@/components/candidates";
import { StateMessage } from "@/components/common";
import { AppControls } from "@/components/controls";
import { ConversationView } from "@/components/conversation";
import { ConversationSidebar } from "@/components/list";
import { useConversationList, useSelection } from "@/hooks";
import type { WorkspaceProps } from "./workspace.types";

export function Workspace({ context }: WorkspaceProps) {
  const state = useConversationList(context.settings);
  const [selectedId, select] = useSelection();
  const sidebar = (
    <div className="flex flex-col">
      <ConversationSidebar state={state} selectedId={selectedId} onSelect={select} />
      {context.canManageCandidates && (
        <div className="px-3 pb-3">
          <CandidatesDialog onChanged={state.list.reload} />
        </div>
      )}
    </div>
  );
  const isLeft = context.settings.sidebarPos !== "right";
  const slots = isLeft
    ? { navigation: sidebar, navigationLabel: "Anfragen" }
    : { detail: sidebar, detailLabel: "Anfragen" };
  return (
    <AppShell brand="Anfragen" skipLinkLabel="Zum Inhalt springen" controls={<AppControls />} {...slots}>
      <PageContainer size="md" className="flex min-h-full flex-col">
        {selectedId === null ? (
          <StateMessage
            title="Keine Anfrage ausgewählt"
            description={`${context.openCount} Anfragen warten auf eine Antwort. Wähle links eine aus.`}
          />
        ) : (
          <ConversationView
            key={selectedId}
            id={selectedId}
            assignees={context.assignees}
            onChanged={state.list.reload}
          />
        )}
      </PageContainer>
    </AppShell>
  );
}
