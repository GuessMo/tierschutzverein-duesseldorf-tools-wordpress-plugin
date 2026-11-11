import { collectionsApi } from "@/api";
import { ConfirmDialog } from "@/components/common";
import { TEXTS } from "@/config/texts";
import type { CollectionDialogsProps } from "./collections.types";
import { CollectionForm } from "./CollectionForm";

export function CollectionDialogs({ state, onClose, onChanged }: CollectionDialogsProps) {
  if (state.type === "create") return <CollectionForm onClose={onClose} onSaved={onChanged} />;
  if (state.type === "edit") {
    return <CollectionForm collection={state.collection} onClose={onClose} onSaved={onChanged} />;
  }
  if (state.type !== "delete") return null;
  const { collection } = state;
  return (
    <ConfirmDialog
      open
      onOpenChange={(open) => !open && onClose()}
      title={`„${collection.name}“ löschen?`}
      description={TEXTS.collectionDelete(collection.entry_count)}
      confirmLabel="Sammlung löschen"
      onConfirm={async () => {
        await collectionsApi.remove(collection.id);
        await onChanged();
      }}
    />
  );
}
