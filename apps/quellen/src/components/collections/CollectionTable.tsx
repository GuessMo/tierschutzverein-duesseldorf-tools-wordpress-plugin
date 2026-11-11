import { Table, TableBody, TableHead, TableHeader, TableRow } from "@web-app-kit/frontend/ui";
import type { CollectionTableProps } from "./collections.types";
import { CollectionRow } from "./CollectionRow";

export function CollectionTable({ collections, onEdit, onDelete }: CollectionTableProps) {
  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Sammlung</TableHead>
          <TableHead className="hidden sm:table-cell">Beschreibung</TableHead>
          <TableHead className="text-right">Quellen</TableHead>
          <TableHead className="w-24 text-right">Aktionen</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {collections.map((collection) => (
          <CollectionRow
            key={collection.id}
            collection={collection}
            onEdit={onEdit}
            onDelete={onDelete}
          />
        ))}
      </TableBody>
    </Table>
  );
}
