import { taxonomiesApi } from "@/api";
import type { Taxonomy, TaxonomyInput } from "@/types";

export function useTaxonomySave(
  collectionId: number,
  taxonomy: Taxonomy | undefined,
  onSaved: () => Promise<void>,
) {
  return async (input: TaxonomyInput) => {
    const trimmed = { ...input, name: input.name.trim() };
    if (taxonomy) await taxonomiesApi.update(taxonomy.id, trimmed);
    else await taxonomiesApi.create(collectionId, trimmed);
    await onSaved();
  };
}
