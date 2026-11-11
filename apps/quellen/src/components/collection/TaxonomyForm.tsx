import { useState } from "react";
import { FormDialog, NameField } from "@/components/common";
import type { TaxonomyFormProps } from "./collection.types";
import { TaxonomyKindField } from "./TaxonomyKindField";
import { useTaxonomySave } from "./useTaxonomySave";

export function TaxonomyForm({ collectionId, taxonomy, onClose, onSaved }: TaxonomyFormProps) {
  const [name, setName] = useState(taxonomy?.name ?? "");
  const [isHierarchical, setIsHierarchical] = useState(taxonomy?.is_hierarchical ?? false);
  const save = useTaxonomySave(collectionId, taxonomy, onSaved);

  return (
    <FormDialog
      open
      onOpenChange={(open) => !open && onClose()}
      title={taxonomy ? "Taxonomie bearbeiten" : "Neue Taxonomie"}
      submitLabel={taxonomy ? "Speichern" : "Taxonomie anlegen"}
      onSubmit={() => save({ name, is_hierarchical: isHierarchical })}
    >
      <NameField id="taxonomy-name" value={name} onValueChange={setName} />
      <TaxonomyKindField
        isHierarchical={isHierarchical}
        wasHierarchical={taxonomy?.is_hierarchical ?? false}
        onChange={setIsHierarchical}
      />
    </FormDialog>
  );
}
