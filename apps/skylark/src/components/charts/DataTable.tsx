import { formatDay } from "@/utils";
import type { CountRow } from "@/types";

interface DataTableProps {
  rows: CountRow[];
  label: string;
  format: (value: number) => string;
}

export function DataTable({ rows, label, format }: DataTableProps) {
  return (
    <table className="sr-only">
      <caption>{label}</caption>
      <thead>
        <tr>
          <th scope="col">Tag</th>
          <th scope="col">Wert</th>
        </tr>
      </thead>
      <tbody>
        {rows.map((row) => (
          <tr key={row.label}>
            <th scope="row">{formatDay(row.label)}</th>
            <td>{format(row.total)}</td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}
