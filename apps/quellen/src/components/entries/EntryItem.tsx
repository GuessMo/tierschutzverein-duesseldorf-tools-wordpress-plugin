import { EditDeleteActions } from "@/components/common";
import { KIND_LABELS } from "@/config/kinds";
import type { EntryItemProps } from "./entries.types";
import { EntryTerms } from "./EntryTerms";
import { EntryTitle } from "./EntryTitle";

export function EntryItem({ entry, terms, onEdit, onDelete }: EntryItemProps) {
  const meta = [KIND_LABELS[entry.kind], entry.publisher, entry.year].filter(Boolean).join(" · ");
  return (
    <li className="flex items-start gap-3 py-4">
      <div className="flex min-w-0 flex-1 flex-col gap-1.5">
        <EntryTitle entry={entry} />
        <p className="text-xs text-muted-foreground">{meta}</p>
        {entry.body && <p className="line-clamp-2 max-w-prose text-sm">{entry.body}</p>}
        <EntryTerms termIds={entry.term_ids} terms={terms} />
      </div>
      <div className="flex shrink-0 gap-1">
        <EditDeleteActions
          name={entry.title}
          onEdit={() => onEdit(entry)}
          onDelete={() => onDelete(entry)}
        />
      </div>
    </li>
  );
}
