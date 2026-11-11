import { StateMessage } from "@/components/common";
import type { EntryResultsProps } from "./entries.types";
import { EntryList } from "./EntryList";
import { NoEntries } from "./NoEntries";

function countLabel(count: number): string {
  return count === 1 ? "1 Quelle" : `${count} Quellen`;
}

function summaryLabel(visible: number, total: number): string {
  return visible === total ? countLabel(total) : `${visible} von ${countLabel(total)}`;
}

export function EntryResults({ total, entries, terms, actions }: EntryResultsProps) {
  if (total === 0) return <NoEntries onCreate={actions.onCreate} />;
  return (
    <div className="flex flex-col gap-2">
      <p role="status" className="text-sm text-muted-foreground">
        {summaryLabel(entries.length, total)}
      </p>
      {entries.length === 0 ? (
        <StateMessage
          title="Keine Treffer"
          description="Ändere die Suche oder setze die Filter zurück."
        />
      ) : (
        <EntryList
          entries={entries}
          terms={terms}
          onEdit={actions.onEdit}
          onDelete={actions.onDelete}
        />
      )}
    </div>
  );
}
