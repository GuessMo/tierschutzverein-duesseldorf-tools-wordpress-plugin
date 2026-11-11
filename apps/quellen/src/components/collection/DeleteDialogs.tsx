import { taxonomiesApi, termsApi } from "@/api";
import { ConfirmDialog } from "@/components/common";
import { TEXTS } from "@/config/texts";
import type { Taxonomy, Term } from "@/types";
import type { DeleteDialogProps } from "./collection.types";

export function TaxonomyDeleteDialog(props: DeleteDialogProps & { taxonomy: Taxonomy }) {
  const { taxonomy, onClose, onDeleted } = props;
  return (
    <ConfirmDialog
      open
      onOpenChange={(open) => !open && onClose()}
      title={`„${taxonomy.name}“ löschen?`}
      description={TEXTS.taxonomyDelete}
      confirmLabel="Taxonomie löschen"
      onConfirm={async () => {
        await taxonomiesApi.remove(taxonomy.id);
        await onDeleted();
      }}
    />
  );
}

export function TermDeleteDialog({ term, onClose, onDeleted }: DeleteDialogProps & { term: Term }) {
  return (
    <ConfirmDialog
      open
      onOpenChange={(open) => !open && onClose()}
      title={`Begriff „${term.name}“ löschen?`}
      description={TEXTS.termDelete}
      confirmLabel="Begriff löschen"
      onConfirm={async () => {
        await termsApi.remove(term.id);
        await onDeleted();
      }}
    />
  );
}
