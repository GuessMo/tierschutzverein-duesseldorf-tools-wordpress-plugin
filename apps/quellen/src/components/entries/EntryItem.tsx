import { IconExternalLink, IconTrash } from "@tabler/icons-react";
import { Button, cn, INTERACTIVE_SURFACE_CLASSES } from "@web-app-kit/frontend/ui";
import { ROW_ACTION_CLASSES } from "@tsvd/tool-controls";
import { KIND_LABELS } from "@/config/kinds";
import type { EntryItemProps } from "./entries.types";
import { EntryTerms } from "./EntryTerms";

const STRETCHED_TARGET_CLASSES = [
  "truncate text-left font-medium outline-none after:absolute after:inset-0 after:rounded-md",
  "hover:underline active:text-primary focus-visible:after:ring-2 focus-visible:after:ring-ring",
].join(" ");

export function EntryItem({ entry, terms, onEdit, onDelete }: EntryItemProps) {
  const meta = [KIND_LABELS[entry.kind], entry.publisher, entry.year].filter(Boolean).join(" · ");
  return (
    <li
      className={cn(
        INTERACTIVE_SURFACE_CLASSES,
        "relative flex items-start gap-3 rounded-md px-3 py-4",
      )}
    >
      <div className="flex min-w-0 flex-1 flex-col gap-1.5">
        <h3 className="text-sm">
          <button type="button" className={STRETCHED_TARGET_CLASSES} onClick={() => onEdit(entry)}>
            {entry.title}
          </button>
        </h3>
        <p className="text-xs text-muted-foreground">{meta}</p>
        {entry.body && <p className="line-clamp-2 max-w-prose text-sm">{entry.body}</p>}
        <EntryTerms termIds={entry.term_ids} terms={terms} />
      </div>
      <div className={cn(ROW_ACTION_CLASSES, "flex shrink-0 gap-1")}>
        {entry.url && (
          <Button asChild variant="ghost" size="icon-sm">
            <a
              href={entry.url}
              target="_blank"
              rel="noopener noreferrer"
              aria-label={`${entry.title}: Quelle öffnen (neuer Tab)`}
              title="Quelle öffnen"
            >
              <IconExternalLink aria-hidden />
            </a>
          </Button>
        )}
        <Button
          variant="ghost"
          size="icon-sm"
          aria-label={`${entry.title} löschen`}
          onClick={() => onDelete(entry)}
        >
          <IconTrash aria-hidden />
        </Button>
      </div>
    </li>
  );
}
