import { useState } from "react";
import { IconPlus } from "@tabler/icons-react";
import { AppShell, Button, PageContainer } from "@web-app-kit/frontend/ui";
import { collectionsApi } from "@/api";
import { AppThemeToggle } from "@/components/controls";
import { useResource } from "@/hooks";
import type { CollectionDialogState } from "./collections.types";
import { CollectionContent } from "./CollectionContent";
import { CollectionDialogs } from "./CollectionDialogs";

export function CollectionsScreen() {
  const collections = useResource(collectionsApi.list);
  const [dialog, setDialog] = useState<CollectionDialogState>({ type: "none" });
  const create = () => setDialog({ type: "create" });

  return (
    <AppShell brand="Quellen" skipLinkLabel="Zum Inhalt springen" controls={<AppThemeToggle />}>
      <PageContainer size="md" className="flex flex-col gap-6">
        <div className="flex flex-wrap items-center justify-between gap-3">
          <h1 className="text-xl font-semibold">Sammlungen</h1>
          <Button onClick={create}>
            <IconPlus data-icon="inline-start" />
            Neue Sammlung
          </Button>
        </div>
        <CollectionContent
          collections={collections}
          onEdit={(collection) => setDialog({ type: "edit", collection })}
          onDelete={(collection) => setDialog({ type: "delete", collection })}
        />
      </PageContainer>
      <CollectionDialogs
        state={dialog}
        onClose={() => setDialog({ type: "none" })}
        onChanged={collections.reload}
      />
    </AppShell>
  );
}
