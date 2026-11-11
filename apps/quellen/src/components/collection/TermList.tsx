import { Button } from "@web-app-kit/frontend/ui";
import { flattenTerms } from "@/lib/terms";
import type { TaxonomySectionProps } from "./collection.types";
import { TermRow } from "./TermRow";

export function TermList({ taxonomy, terms, onOpen }: TaxonomySectionProps) {
  const nodes = flattenTerms(terms);
  const addFirst = () => onOpen({ type: "term-create", taxonomy, parentId: null });

  if (nodes.length === 0) {
    return (
      <Button variant="link" size="sm" className="self-start" onClick={addFirst}>
        Ersten Begriff hinzufügen
      </Button>
    );
  }
  return (
    <ul className="flex flex-col">
      {nodes.map(({ term, depth }) => (
        <TermRow key={term.id} taxonomy={taxonomy} term={term} depth={depth} onOpen={onOpen} />
      ))}
    </ul>
  );
}
