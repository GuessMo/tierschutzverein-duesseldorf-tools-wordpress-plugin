import { AppShell } from "@web-app-kit/frontend/ui";
import { AppThemeToggle } from "@/components/controls";
import { EntriesView } from "@/components/entries";
import { useWorkspace } from "@/hooks";
import { CollectionHeader } from "./CollectionHeader";
import { entryDetail } from "./EntryDetail";
import { MissingCollection } from "./MissingCollection";
import { TaxonomyPanel } from "./TaxonomyPanel";
import { useEntryEditor } from "./useEntryEditor";

export function CollectionScreen({ collectionId }: { collectionId: number }) {
  const workspace = useWorkspace(collectionId);
  const { editor, close, openNew, openEntry } = useEntryEditor();

  if (workspace.collection.error) return <MissingCollection message={workspace.collection.error} />;
  return (
    <AppShell
      brand="Quellen"
      homeHref="#"
      skipLinkLabel="Zum Inhalt springen"
      actions={<CollectionHeader name={workspace.collection.data?.name ?? ""} />}
      controls={<AppThemeToggle />}
      navigation={<TaxonomyPanel workspace={workspace} />}
      navigationLabel="Taxonomien"
      {...entryDetail(workspace, editor, close)}
    >
      <EntriesView workspace={workspace} onCreate={openNew} onEdit={openEntry} />
    </AppShell>
  );
}
