import { termsApi } from "@/api";
import type { Taxonomy, Term } from "@/types";

export function useTermSave(
  taxonomy: Taxonomy,
  term: Term | undefined,
  onSaved: () => Promise<void>,
) {
  return async (name: string, parentId: number | null) => {
    const input = { name: name.trim(), parent_id: taxonomy.is_hierarchical ? parentId : null };
    if (term) await termsApi.update(term.id, input);
    else await termsApi.create(taxonomy.id, input);
    await onSaved();
  };
}
