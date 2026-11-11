import type { Entry, Term } from "@/types";
import type { EntryFilter } from "@/types/filter";
import { descendantIds } from "./terms";

function matchesSearch(entry: Entry, search: string): boolean {
  const needle = search.trim().toLocaleLowerCase("de");
  if (!needle) return true;
  const haystack = [entry.title, entry.body, entry.publisher].join(" ").toLocaleLowerCase("de");
  return haystack.includes(needle);
}

function matchesTerms(entry: Entry, terms: Term[], filter: EntryFilter): boolean {
  return Object.values(filter.termByTaxonomy).every((termId) => {
    const accepted = descendantIds(terms, termId);
    return entry.term_ids.some((id) => accepted.has(id));
  });
}

export function filterEntries(entries: Entry[], terms: Term[], filter: EntryFilter): Entry[] {
  return entries.filter(
    (entry) => matchesSearch(entry, filter.search) && matchesTerms(entry, terms, filter),
  );
}

export function isFilterActive(filter: EntryFilter): boolean {
  return filter.search.trim() !== "" || Object.keys(filter.termByTaxonomy).length > 0;
}

export const EMPTY_FILTER: EntryFilter = { search: "", termByTaxonomy: {} };

export function withTerm(filter: EntryFilter, taxonomyId: number, termId?: number): EntryFilter {
  const termByTaxonomy = { ...filter.termByTaxonomy };
  if (termId === undefined) delete termByTaxonomy[taxonomyId];
  else termByTaxonomy[taxonomyId] = termId;
  return { ...filter, termByTaxonomy };
}
