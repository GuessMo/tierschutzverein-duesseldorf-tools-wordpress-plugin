import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableFooter,
  TableHead,
  TableHeader,
  TableRow,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

const ROWS = [
  { name: "Startseite", owner: "Lena", count: 4 },
  { name: "Checkout", owner: "Tarek", count: 7 },
  { name: "Suche", owner: "Mira", count: 2 },
];

function TableDefault() {
  return (
    <Table>
      <TableCaption>Offene Entwürfe je Bereich</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead>Bereich</TableHead>
          <TableHead>Verantwortlich</TableHead>
          <TableHead className="text-right">Entwürfe</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {ROWS.map((row, index) => (
          <TableRow key={row.name} data-state={index === 1 ? "selected" : undefined}>
            <TableCell>{row.name}</TableCell>
            <TableCell>{row.owner}</TableCell>
            <TableCell className="text-right tabular-nums">{row.count}</TableCell>
          </TableRow>
        ))}
      </TableBody>
      <TableFooter>
        <TableRow>
          <TableCell colSpan={2}>Summe</TableCell>
          <TableCell className="text-right tabular-nums">13</TableCell>
        </TableRow>
      </TableFooter>
    </Table>
  );
}

export const tableEntry: CatalogEntry = {
  slug: "table",
  name: "Table",
  group: "actions",
  status: "stable",
  description:
    "Mehrspaltige Daten. TableCaption oder aria-label benennt die Tabelle; eine ausgewählte " +
    'Zeile trägt data-state="selected".',
  exports: [
    "Table",
    "TableHeader",
    "TableBody",
    "TableFooter",
    "TableRow",
    "TableHead",
    "TableCell",
    "TableCaption",
  ],
  variants: [
    {
      key: "default",
      title: "Mit Beschriftung, Auswahl und Summe",
      description: "Zweite Zeile ausgewählt, Zahlen rechtsbündig mit tabular-nums.",
      Preview: TableDefault,
    },
  ],
  states: [
    { name: "Hover", description: "Zeile hebt sich beim Überfahren ab." },
    { name: "Ausgewählt", description: 'data-state="selected" markiert die Zeile dauerhaft.' },
  ],
};
