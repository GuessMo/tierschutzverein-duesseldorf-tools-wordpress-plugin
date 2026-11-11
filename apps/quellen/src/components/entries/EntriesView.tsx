import { useState } from "react";
import { PageContainer } from "@web-app-kit/frontend/ui";
import { EMPTY_FILTER } from "@/lib/filterEntries";
import type { Entry } from "@/types";
import type { EntriesViewProps } from "./entries.types";
import { EntriesBody } from "./EntriesBody";
import { EntriesHeader } from "./EntriesHeader";
import { EntryDeleteDialog } from "./EntryDeleteDialog";
import { EntryFilters } from "./EntryFilters";

export function EntriesView({ workspace, onCreate, onEdit }: EntriesViewProps) {
  const { entries, taxonomies, terms, collection } = workspace;
  const [filter, setFilter] = useState(EMPTY_FILTER);
  const [toDelete, setToDelete] = useState<Entry | null>(null);
  const refresh = async () => {
    await Promise.all([entries.reload(), collection.reload()]);
  };

  return (
    <PageContainer size="lg" className="flex flex-col gap-6">
      <EntriesHeader onCreate={onCreate} />
      <EntryFilters
        taxonomies={taxonomies.data ?? []}
        terms={terms.data ?? []}
        filter={filter}
        onChange={setFilter}
      />
      <EntriesBody
        workspace={workspace}
        filter={filter}
        actions={{ onCreate, onEdit, onDelete: setToDelete }}
      />
      {toDelete && (
        <EntryDeleteDialog entry={toDelete} onClose={() => setToDelete(null)} onDeleted={refresh} />
      )}
    </PageContainer>
  );
}
