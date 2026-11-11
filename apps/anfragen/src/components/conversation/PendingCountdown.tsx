import { useCountdown } from "@/hooks";

export function PendingCountdown({ seconds, onDone }: { seconds: number; onDone: () => void }) {
  const remaining = useCountdown(seconds, onDone);
  return (
    <span role="status" className="text-xs text-muted-foreground tabular-nums">
      {remaining > 0 ? `Wird in ${remaining} s gesendet` : "Wird gesendet …"}
    </span>
  );
}
