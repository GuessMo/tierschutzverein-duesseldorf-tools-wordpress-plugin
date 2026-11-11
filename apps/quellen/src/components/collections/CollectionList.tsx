import type { CollectionListProps } from "./collections.types";
import { CollectionRow } from "./CollectionRow";

function totalLabel(count: number): string {
  return count === 1 ? "1 Sammlung" : `${count} Sammlungen`;
}

export function CollectionList({ collections, onEdit, onDelete }: CollectionListProps) {
  return (
    <div className="flex flex-col gap-3">
      <p className="text-sm text-muted-foreground">{totalLabel(collections.length)}</p>
      <ul className="flex flex-col gap-1">
        {collections.map((collection) => (
          <CollectionRow
            key={collection.id}
            collection={collection}
            onEdit={onEdit}
            onDelete={onDelete}
          />
        ))}
      </ul>
    </div>
  );
}
