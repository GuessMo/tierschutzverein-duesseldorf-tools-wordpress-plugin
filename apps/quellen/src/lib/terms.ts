import type { Term } from "@/types";

export interface TermNode {
  term: Term;
  depth: number;
}

export function termsOf(terms: Term[], taxonomyId: number): Term[] {
  return terms.filter((term) => term.taxonomy_id === taxonomyId);
}

export function flattenTerms(terms: Term[]): TermNode[] {
  const ids = new Set(terms.map((term) => term.id));
  const parentOf = (term: Term) =>
    term.parent_id !== null && ids.has(term.parent_id) ? term.parent_id : null;
  const visit = (parentId: number | null, depth: number): TermNode[] =>
    terms
      .filter((term) => parentOf(term) === parentId)
      .flatMap((term) => [{ term, depth }, ...visit(term.id, depth + 1)]);
  return visit(null, 0);
}

export function descendantIds(terms: Term[], rootId: number): Set<number> {
  const result = new Set([rootId]);
  let isGrowing = true;
  while (isGrowing) {
    const before = result.size;
    terms.forEach((term) => {
      if (term.parent_id !== null && result.has(term.parent_id)) result.add(term.id);
    });
    isGrowing = result.size > before;
  }
  return result;
}

export function termName(terms: Term[], id: number): string | undefined {
  return terms.find((term) => term.id === id)?.name;
}
