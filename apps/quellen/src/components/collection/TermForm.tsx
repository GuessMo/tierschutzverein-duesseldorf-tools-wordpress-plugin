import { useState } from "react";
import { FormDialog, NameField } from "@/components/common";
import type { TermFormProps } from "./collection.types";
import { ParentSelect } from "./ParentSelect";
import { useTermSave } from "./useTermSave";

export function TermForm({ taxonomy, terms, term, parentId, onClose, onSaved }: TermFormProps) {
  const [name, setName] = useState(term?.name ?? "");
  const [parent, setParent] = useState<number | null>(term?.parent_id ?? parentId ?? null);
  const save = useTermSave(taxonomy, term, onSaved);

  return (
    <FormDialog
      open
      onOpenChange={(open) => !open && onClose()}
      title={term ? "Begriff bearbeiten" : `Neuer Begriff in „${taxonomy.name}“`}
      submitLabel={term ? "Speichern" : "Begriff anlegen"}
      onSubmit={() => save(name, parent)}
    >
      <NameField id="term-name" value={name} onValueChange={setName} />
      {taxonomy.is_hierarchical && (
        <ParentSelect terms={terms} termId={term?.id} value={parent} onChange={setParent} />
      )}
    </FormDialog>
  );
}
