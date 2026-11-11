import {
  Button,
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@web-app-kit/frontend/ui";
import type { CountRow } from "@/types";
import { formatNumber } from "@tsvd/tool-controls";

export function AllUrlsDialog({ title, rows }: { title: string; rows: CountRow[] }) {
  if (rows.length === 0) return null;
  return (
    <Dialog>
      <DialogTrigger asChild>
        <Button variant="outline" size="sm">
          Alle URLs anzeigen
        </Button>
      </DialogTrigger>
      <DialogContent closeLabel="Schließen" className="max-h-[80vh] overflow-y-auto sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
        </DialogHeader>
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b text-left text-muted-foreground">
              <th scope="col" className="py-2 font-medium">
                URL
              </th>
              <th scope="col" className="py-2 text-right font-medium">
                Aufrufe
              </th>
            </tr>
          </thead>
          <tbody>
            {rows.map((row) => (
              <tr key={row.label} className="border-b last:border-0">
                <td className="py-2 break-all">{row.label}</td>
                <td className="py-2 text-right tabular-nums">{formatNumber(row.total)}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </DialogContent>
    </Dialog>
  );
}
