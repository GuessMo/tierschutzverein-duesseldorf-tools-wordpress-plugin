import { Badge } from "@web-app-kit/frontend/ui";
import { termName } from "@/lib/terms";
import type { Term } from "@/types";

export function EntryTerms({ termIds, terms }: { termIds: number[]; terms: Term[] }) {
  const labels = termIds.flatMap((id) => {
    const name = termName(terms, id);
    return name ? [{ id, name }] : [];
  });
  if (labels.length === 0) return null;
  return (
    <ul className="flex flex-wrap gap-1" aria-label="Begriffe">
      {labels.map((label) => (
        <li key={label.id}>
          <Badge variant="outline" className="border-input bg-accent">
            {label.name}
          </Badge>
        </li>
      ))}
    </ul>
  );
}
