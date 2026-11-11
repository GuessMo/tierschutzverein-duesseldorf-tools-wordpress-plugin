import { Badge } from "@web-app-kit/frontend/ui";
import type { StatusOption, Tone } from "@/types";

const TONE_VARIANTS = {
  success: "default",
  warning: "warning",
  danger: "destructive",
  info: "info",
} as const satisfies Record<Tone, string>;

export function StateBadge({ status }: { status: StatusOption | undefined }) {
  if (!status) return <span className="text-muted-foreground">–</span>;
  return <Badge variant={TONE_VARIANTS[status.tone]}>{status.label}</Badge>;
}
