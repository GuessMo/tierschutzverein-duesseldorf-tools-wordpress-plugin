import type {
  Collection,
  CollectionInput,
  DeleteResult,
  Entry,
  EntryInput,
  Taxonomy,
  TaxonomyInput,
  Term,
  TermInput,
} from "@/types";
import { send } from "./client";

const BASE = "sources";

export const collectionsApi = {
  list: () => send<Collection[]>("GET", `${BASE}/collections`),
  get: (id: number) => send<Collection>("GET", `${BASE}/collections/${id}`),
  create: (input: CollectionInput) => send<Collection>("POST", `${BASE}/collections`, input),
  update: (id: number, input: CollectionInput) =>
    send<Collection>("PATCH", `${BASE}/collections/${id}`, input),
  remove: (id: number) => send<DeleteResult>("DELETE", `${BASE}/collections/${id}`),
};

export const taxonomiesApi = {
  list: (collectionId: number) =>
    send<Taxonomy[]>("GET", `${BASE}/collections/${collectionId}/taxonomies`),
  create: (collectionId: number, input: TaxonomyInput) =>
    send<Taxonomy>("POST", `${BASE}/collections/${collectionId}/taxonomies`, input),
  update: (id: number, input: TaxonomyInput) =>
    send<Taxonomy>("PATCH", `${BASE}/taxonomies/${id}`, input),
  remove: (id: number) => send<DeleteResult>("DELETE", `${BASE}/taxonomies/${id}`),
};

export const termsApi = {
  list: (collectionId: number) => send<Term[]>("GET", `${BASE}/collections/${collectionId}/terms`),
  create: (taxonomyId: number, input: TermInput) =>
    send<Term>("POST", `${BASE}/taxonomies/${taxonomyId}/terms`, input),
  update: (id: number, input: TermInput) => send<Term>("PATCH", `${BASE}/terms/${id}`, input),
  remove: (id: number) => send<DeleteResult>("DELETE", `${BASE}/terms/${id}`),
};

export const entriesApi = {
  list: (collectionId: number) =>
    send<Entry[]>("GET", `${BASE}/collections/${collectionId}/entries`),
  create: (collectionId: number, input: EntryInput) =>
    send<Entry>("POST", `${BASE}/collections/${collectionId}/entries`, input),
  update: (id: number, input: EntryInput) => send<Entry>("PATCH", `${BASE}/entries/${id}`, input),
  remove: (id: number) => send<DeleteResult>("DELETE", `${BASE}/entries/${id}`),
};
