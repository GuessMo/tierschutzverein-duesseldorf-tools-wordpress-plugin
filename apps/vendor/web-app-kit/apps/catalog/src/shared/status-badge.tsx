import { Badge } from "@web-app-kit/frontend/ui";
import type { CatalogStatus } from "../catalog/catalog.types";
import { STATUS_LABELS } from "../catalog/groups";

export function StatusBadge({ status }: { status: CatalogStatus }) {
  return (
    <Badge variant={status === "stable" ? "outline" : "secondary"}>{STATUS_LABELS[status]}</Badge>
  );
}
