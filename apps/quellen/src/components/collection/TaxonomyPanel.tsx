import { useState } from "react";
import { IconPlus } from "@tabler/icons-react";
import { Button, SidePanel } from "@web-app-kit/frontend/ui";
import type { TaxonomyDialogState, WorkspaceProps } from "./collection.types";
import { TaxonomyDialogs } from "./TaxonomyDialogs";
import { TaxonomyList } from "./TaxonomyList";

export function TaxonomyPanel({ workspace }: WorkspaceProps) {
  const [dialog, setDialog] = useState<TaxonomyDialogState>({ type: "none" });
  const create = () => setDialog({ type: "taxonomy-create" });

  return (
    <SidePanel
      side="left"
      width="md"
      title="Taxonomien"
      actions={
        <Button variant="ghost" size="icon-sm" aria-label="Taxonomie anlegen" onClick={create}>
          <IconPlus />
        </Button>
      }
    >
      <TaxonomyList workspace={workspace} onOpen={setDialog} onCreate={create} />
      <TaxonomyDialogs
        workspace={workspace}
        state={dialog}
        onClose={() => setDialog({ type: "none" })}
      />
    </SidePanel>
  );
}
