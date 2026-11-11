import type { EntryListProps } from "./entries.types";
import { EntryItem } from "./EntryItem";

export function EntryList({ entries, terms, onEdit, onDelete }: EntryListProps) {
  return (
    <ul className="flex flex-col divide-y border-y">
      {entries.map((entry) => (
        <EntryItem key={entry.id} entry={entry} terms={terms} onEdit={onEdit} onDelete={onDelete} />
      ))}
    </ul>
  );
}
