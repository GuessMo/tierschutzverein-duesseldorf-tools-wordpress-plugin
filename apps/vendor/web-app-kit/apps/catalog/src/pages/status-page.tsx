import {
  PageContainer,
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@web-app-kit/frontend/ui";
import { CATALOG_ENTRIES } from "../catalog/entries";
import { GROUP_LABELS } from "../catalog/groups";
import { componentPath } from "../routing/paths";
import { PageHeader } from "../shared/page-header";
import { StatusBadge } from "../shared/status-badge";
import { TextLink } from "../shared/text-link";

const ROWS = [...CATALOG_ENTRIES].sort((first, second) =>
  first.name.localeCompare(second.name, "de"),
);

export function StatusPage() {
  return (
    <PageContainer size="lg" className="flex flex-col gap-8">
      <PageHeader title="Statusübersicht">
        Stabil heißt: im Kit released und in Tools einsetzbar. Beta-Bausteine sind in Erprobung,
        auslaufende werden mit der nächsten Major-Version entfernt.
      </PageHeader>
      <Table>
        <TableCaption>Status aller Kit-Bausteine</TableCaption>
        <TableHeader>
          <TableRow>
            <TableHead>Baustein</TableHead>
            <TableHead>Gruppe</TableHead>
            <TableHead>Status</TableHead>
            <TableHead className="text-right">Exporte</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {ROWS.map((entry) => (
            <TableRow key={entry.slug}>
              <TableCell>
                <TextLink href={componentPath(entry.slug)}>{entry.name}</TextLink>
              </TableCell>
              <TableCell>{GROUP_LABELS[entry.group]}</TableCell>
              <TableCell>
                <StatusBadge status={entry.status} />
              </TableCell>
              <TableCell className="text-right tabular-nums">{entry.exports.length}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </PageContainer>
  );
}
