import { entriesApi } from "@/api";
import { ConfirmDialog } from "@/components/common";
import type { EntryDeleteDialogProps } from "./entries.types";

export function EntryDeleteDialog({ entry, onClose, onDeleted }: EntryDeleteDialogProps) {
  return (
    <ConfirmDialog
      open
      onOpenChange={(open) => !open && onClose()}
      title={`„${entry.title}“ löschen?`}
      description="Die Quelle wird endgültig aus dieser Sammlung gelöscht."
      confirmLabel="Quelle löschen"
      onConfirm={async () => {
        await entriesApi.remove(entry.id);
        await onDeleted();
      }}
    />
  );
}
