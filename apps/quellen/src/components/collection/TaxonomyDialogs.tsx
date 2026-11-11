import { termsOf } from "@/lib/terms";
import type { TaxonomyDialogsProps } from "./collection.types";
import { TaxonomyDeleteDialog, TermDeleteDialog } from "./DeleteDialogs";
import { TaxonomyForm } from "./TaxonomyForm";
import { TermForm } from "./TermForm";

export function TaxonomyDialogs({ workspace, state, onClose }: TaxonomyDialogsProps) {
  const { collection, taxonomies, terms, entries } = workspace;
  const refresh = async () => {
    await Promise.all([taxonomies.reload(), terms.reload(), entries.reload()]);
  };
  const common = { onClose, onSaved: refresh };
  const collectionId = collection.data?.id ?? 0;

  if (state.type === "taxonomy-create")
    return <TaxonomyForm {...common} collectionId={collectionId} />;
  if (state.type === "taxonomy-edit") {
    return <TaxonomyForm {...common} collectionId={collectionId} taxonomy={state.taxonomy} />;
  }
  if (state.type === "taxonomy-delete") {
    return <TaxonomyDeleteDialog taxonomy={state.taxonomy} onClose={onClose} onDeleted={refresh} />;
  }
  if (state.type === "term-delete") {
    return <TermDeleteDialog term={state.term} onClose={onClose} onDeleted={refresh} />;
  }
  if (state.type !== "term-create" && state.type !== "term-edit") return null;
  return (
    <TermForm
      {...common}
      taxonomy={state.taxonomy}
      terms={termsOf(terms.data ?? [], state.taxonomy.id)}
      term={state.type === "term-edit" ? state.term : undefined}
      parentId={state.type === "term-create" ? state.parentId : undefined}
    />
  );
}
