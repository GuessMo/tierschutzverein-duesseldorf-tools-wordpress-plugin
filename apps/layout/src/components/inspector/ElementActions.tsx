import { Button } from "@web-app-kit/frontend/ui";
import { IconArrowDown, IconArrowUp, IconCopy, IconTrash } from "@tabler/icons-react";
import { duplicateElement, moveElement, removeElement } from "@/services/elementOperations";
import { useEditorStore } from "@/store/editorStore";
import type { ElementInspectorProps } from "./inspector.types";

export function ElementActions({ section, cell, element }: ElementInspectorProps) {
  const { applyPage, select } = useEditorStore.getState();
  const address = { sectionId: section.id, cellId: cell.id };

  const handleRemove = () => {
    applyPage((document) => removeElement(document, address, element.id));
    select({ type: "cell", ...address });
  };

  return (
    <div className="flex flex-wrap gap-2 border-t pt-4">
      <Button
        variant="outline"
        size="icon-sm"
        aria-label="Nach oben"
        onClick={() => applyPage((document) => moveElement(document, address, element.id, -1))}
      >
        <IconArrowUp />
      </Button>
      <Button
        variant="outline"
        size="icon-sm"
        aria-label="Nach unten"
        onClick={() => applyPage((document) => moveElement(document, address, element.id, 1))}
      >
        <IconArrowDown />
      </Button>
      <Button
        variant="outline"
        size="sm"
        onClick={() => applyPage((document) => duplicateElement(document, address, element.id))}
      >
        <IconCopy data-icon="inline-start" />
        Duplizieren
      </Button>
      <Button variant="destructive" size="sm" onClick={handleRemove}>
        <IconTrash data-icon="inline-start" />
        Löschen
      </Button>
    </div>
  );
}
