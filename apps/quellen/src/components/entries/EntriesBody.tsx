import { ErrorMessage, LoadingRows } from "@/components/common";
import { filterEntries } from "@/lib/filterEntries";
import type { EntriesBodyProps } from "./entries.types";
import { EntryResults } from "./EntryResults";

export function EntriesBody({ workspace, filter, actions }: EntriesBodyProps) {
  const { entries, terms } = workspace;
  const allTerms = terms.data ?? [];

  if (entries.error) {
    return <ErrorMessage message={entries.error} onRetry={() => void entries.reload()} />;
  }
  if (!entries.data) return <LoadingRows label="Quellen werden geladen" />;
  return (
    <EntryResults
      total={entries.data.length}
      entries={filterEntries(entries.data, allTerms, filter)}
      terms={allTerms}
      actions={actions}
    />
  );
}
