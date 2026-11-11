import { Badge } from "@web-app-kit/frontend/ui";
import { formatDateTime } from "@/lib";

export function IssueStatus({ sentAt }: { sentAt: string }) {
  if (!sentAt) return <Badge variant="secondary">Entwurf</Badge>;
  return <Badge>Gesendet am {formatDateTime(sentAt)}</Badge>;
}
