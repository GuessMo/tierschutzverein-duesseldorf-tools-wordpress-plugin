import { IconBooks } from "@tabler/icons-react";
import { cn, INTERACTIVE_SURFACE_CLASSES } from "@web-app-kit/frontend/ui";
import { LIST_ROW_CLASSES, ROW_ACTION_CLASSES } from "@tsvd/tool-controls";
import { EditDeleteActions } from "@/components/common";
import { collectionHash } from "@/hooks";
import type { CollectionRowProps } from "./collections.types";

const STRETCHED_TARGET_CLASSES = [
  "truncate text-left font-medium outline-none after:absolute after:inset-0 after:rounded-md",
  "hover:underline active:text-primary focus-visible:after:ring-2 focus-visible:after:ring-ring",
].join(" ");

const ROW_CLASSES = [
  LIST_ROW_CLASSES,
  "grid-cols-[auto_minmax(0,1fr)_auto]",
  "md:grid-cols-[auto_minmax(0,2fr)_minmax(0,3fr)_6rem_auto]",
].join(" ");

function countLabel(count: number): string {
  return count === 1 ? "1 Quelle" : `${count} Quellen`;
}

export function CollectionRow({ collection, onEdit, onDelete }: CollectionRowProps) {
  return (
    <li className={cn(INTERACTIVE_SURFACE_CLASSES, ROW_CLASSES)}>
      <span className="flex size-10 items-center justify-center rounded-md bg-primary-subtle text-primary-subtle-foreground">
        <IconBooks aria-hidden className="size-5" stroke={1.5} />
      </span>
      <span className="flex min-w-0 flex-col">
        <a href={collectionHash(collection.id)} className={STRETCHED_TARGET_CLASSES}>
          {collection.name}
        </a>
        <span className="truncate text-xs text-muted-foreground md:hidden">
          {countLabel(collection.entry_count)}
        </span>
      </span>
      <span className="hidden truncate text-sm text-muted-foreground md:block">
        {collection.description}
      </span>
      <span className="hidden text-sm tabular-nums md:block">
        {countLabel(collection.entry_count)}
      </span>
      <span className={cn(ROW_ACTION_CLASSES, "flex gap-1")}>
        <EditDeleteActions
          name={collection.name}
          onEdit={() => onEdit(collection)}
          onDelete={() => onDelete(collection)}
        />
      </span>
    </li>
  );
}
