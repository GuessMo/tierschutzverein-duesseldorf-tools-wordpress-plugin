import { useState } from "react";
import { collectionsApi } from "@/api";
import { FormDialog } from "@/components/common";
import type { CollectionInput } from "@/types";
import type { CollectionFormProps } from "./collections.types";
import { CollectionFields } from "./CollectionFields";

export function CollectionForm({ collection, onClose, onSaved }: CollectionFormProps) {
  const [input, setInput] = useState<CollectionInput>({
    name: collection?.name ?? "",
    description: collection?.description ?? "",
  });

  const save = async () => {
    const trimmed = { name: input.name.trim(), description: input.description.trim() };
    if (collection) await collectionsApi.update(collection.id, trimmed);
    else await collectionsApi.create(trimmed);
    await onSaved();
  };

  return (
    <FormDialog
      open
      onOpenChange={(open) => !open && onClose()}
      title={collection ? "Sammlung bearbeiten" : "Neue Sammlung"}
      description="Eine Sammlung bündelt Quellen zu einem Projekt oder Thema."
      submitLabel={collection ? "Speichern" : "Sammlung anlegen"}
      onSubmit={save}
    >
      <CollectionFields {...input} onChange={setInput} />
    </FormDialog>
  );
}
