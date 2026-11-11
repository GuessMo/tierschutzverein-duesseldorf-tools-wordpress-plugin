export type EntryKind = "study" | "law" | "statement" | "press" | "book" | "other";

export interface Collection {
  id: number;
  name: string;
  description: string;
  shared_with: string[];
  entry_count: number;
  updated_at: string;
}

export interface Taxonomy {
  id: number;
  collection_id: number;
  name: string;
  is_hierarchical: boolean;
}

export interface Term {
  id: number;
  taxonomy_id: number;
  parent_id: number | null;
  name: string;
}

export interface Entry {
  id: number;
  collection_id: number;
  title: string;
  body: string;
  url: string;
  publisher: string;
  year: number | null;
  kind: EntryKind;
  accessed_at: string | null;
  term_ids: number[];
  updated_at: string;
}

export interface CollectionInput {
  name: string;
  description: string;
}

export interface TaxonomyInput {
  name: string;
  is_hierarchical?: boolean;
}

export interface TermInput {
  name: string;
  parent_id: number | null;
}

export type EntryInput = Omit<Entry, "id" | "collection_id" | "updated_at">;

export interface DeleteResult {
  deleted: boolean;
  id: number;
}
