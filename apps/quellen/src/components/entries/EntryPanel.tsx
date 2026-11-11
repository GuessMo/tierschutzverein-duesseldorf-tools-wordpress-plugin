import { SidePanel } from "@web-app-kit/frontend/ui";
import { useEscape } from "@/hooks";
import type { EntryPanelProps } from "./entries.types";
import { EntryFields } from "./EntryFields";
import { FormActions } from "./FormActions";
import { TermPicker } from "./TermPicker";
import { useEntryForm } from "./useEntryForm";

export function EntryPanel({ workspace, entry, onClose }: EntryPanelProps) {
  const { draft, patch, isBusy, error, handleSubmit } = useEntryForm(workspace, entry, onClose);
  useEscape(onClose);

  return (
    <SidePanel
      side="right"
      width="lg"
      title={entry ? "Quelle bearbeiten" : "Neue Quelle"}
      onOpenChange={(open) => !open && onClose()}
      closeLabel="Schließen"
    >
      <form onSubmit={handleSubmit} className="flex flex-col gap-4 p-2">
        <EntryFields draft={draft} onChange={patch} />
        <TermPicker
          taxonomies={workspace.taxonomies.data ?? []}
          terms={workspace.terms.data ?? []}
          selected={draft.term_ids}
          onChange={(term_ids) => patch({ term_ids })}
        />
        <FormActions isBusy={isBusy} error={error} onCancel={onClose} />
      </form>
    </SidePanel>
  );
}
