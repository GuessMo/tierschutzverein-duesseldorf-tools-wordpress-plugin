import { Table, TableBody, TableHead, TableHeader, TableRow } from "@web-app-kit/frontend/ui";
import { AnimalRow } from "./AnimalRow";
import type { AnimalTableProps } from "./list.types";

export function AnimalTable({ animals, options }: AnimalTableProps) {
  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Tier</TableHead>
          <TableHead className="hidden md:table-cell">Tierart / Rasse</TableHead>
          <TableHead>Status</TableHead>
          <TableHead className="hidden sm:table-cell">Sichtbarkeit</TableHead>
          <TableHead className="hidden text-right lg:table-cell">Geändert</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {animals.map((animal) => (
          <AnimalRow key={animal.id} animal={animal} options={options} />
        ))}
      </TableBody>
    </Table>
  );
}
