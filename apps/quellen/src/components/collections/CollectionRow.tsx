import { Button, TableCell, TableRow } from "@web-app-kit/frontend/ui";
import { EditDeleteActions } from "@/components/common";
import { collectionHash } from "@/hooks";
import type { CollectionRowProps } from "./collections.types";

export function CollectionRow({ collection, onEdit, onDelete }: CollectionRowProps) {
  return (
    <TableRow>
      <TableCell>
        <Button asChild variant="link" className="h-auto px-0 text-foreground">
          <a href={collectionHash(collection.id)}>{collection.name}</a>
        </Button>
      </TableCell>
      <TableCell className="hidden max-w-md truncate text-muted-foreground sm:table-cell">
        {collection.description}
      </TableCell>
      <TableCell className="text-right tabular-nums">{collection.entry_count}</TableCell>
      <TableCell className="text-right">
        <EditDeleteActions
          name={collection.name}
          onEdit={() => onEdit(collection)}
          onDelete={() => onDelete(collection)}
        />
      </TableCell>
    </TableRow>
  );
}
