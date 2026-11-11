import { useCallback } from "react";
import { collectionsApi, entriesApi, taxonomiesApi, termsApi } from "@/api";
import { useResource } from "./useResource";

export function useWorkspace(collectionId: number) {
  const loadCollection = useCallback(() => collectionsApi.get(collectionId), [collectionId]);
  const loadTaxonomies = useCallback(() => taxonomiesApi.list(collectionId), [collectionId]);
  const loadTerms = useCallback(() => termsApi.list(collectionId), [collectionId]);
  const loadEntries = useCallback(() => entriesApi.list(collectionId), [collectionId]);

  return {
    collection: useResource(loadCollection),
    taxonomies: useResource(loadTaxonomies),
    terms: useResource(loadTerms),
    entries: useResource(loadEntries),
  };
}

export type Workspace = ReturnType<typeof useWorkspace>;
