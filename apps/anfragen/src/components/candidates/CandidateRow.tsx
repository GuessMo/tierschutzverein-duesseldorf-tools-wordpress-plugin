import { Badge, Button } from "@web-app-kit/frontend/ui";
import type { CandidateRowProps } from "./candidates.types";

export function CandidateRow({ candidate, onDecide }: CandidateRowProps) {
  return (
    <li className="flex flex-col gap-2 rounded-lg border p-3">
      <div className="flex flex-wrap items-center gap-2 text-sm">
        <span className="font-medium">{candidate.sender}</span>
        <span className="text-muted-foreground">{candidate.receivedLabel}</span>
        {candidate.isSpamFolder && <Badge variant="warning">Aus dem Spam-Ordner</Badge>}
      </div>
      <p className="text-sm font-medium">{candidate.subject}</p>
      <p className="line-clamp-3 text-sm text-muted-foreground">{candidate.excerpt}</p>
      <div className="flex gap-2">
        <Button size="sm" onClick={() => onDecide(candidate.id, "approve")}>
          Zuordnen
        </Button>
        <Button size="sm" variant="outline" onClick={() => onDecide(candidate.id, "reject")}>
          Verwerfen
        </Button>
      </div>
    </li>
  );
}
