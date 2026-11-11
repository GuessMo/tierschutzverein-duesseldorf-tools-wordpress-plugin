import { useEffect } from "react";
import { removeElement } from "@/services/elementOperations";
import { removeSection } from "@/services/sectionOperations";
import { useEditorStore } from "@/store/editorStore";

function isTyping(target: EventTarget | null): boolean {
  return (
    target instanceof HTMLElement &&
    target.closest("input, textarea, select, [contenteditable]") !== null
  );
}

function deleteSelection(): void {
  const { selection, applyPage, select } = useEditorStore.getState();
  if (selection.type === "element") {
    applyPage((document) => removeElement(document, selection, selection.elementId));
  }
  if (selection.type === "section") {
    applyPage((document) => removeSection(document, selection.sectionId));
  }
  select({ type: "none" });
}

function handleKeyDown(event: KeyboardEvent): void {
  if (isTyping(event.target)) return;
  const { undo, redo, select } = useEditorStore.getState();
  const isModifier = event.metaKey || event.ctrlKey;
  if (isModifier && event.key.toLowerCase() === "z") {
    event.preventDefault();
    if (event.shiftKey) redo();
    else undo();
    return;
  }
  if (event.key === "Escape") select({ type: "none" });
  if (event.key === "Delete" || event.key === "Backspace") deleteSelection();
}

export function useEditorShortcuts(): void {
  useEffect(() => {
    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, []);
}
