import { Button, Dialog, DialogContent, DialogFooter } from "@web-app-kit/frontend/ui";
import type { ConfirmDialogProps } from "./common.types";
import { DialogHeading } from "./DialogHeading";
import { FormError } from "./FormError";
import { useSubmitState } from "./useSubmitState";

export function ConfirmDialog(props: ConfirmDialogProps) {
  const { open, onOpenChange, title, description, confirmLabel, onConfirm } = props;
  const { isBusy, error, run, resetOnClose } = useSubmitState();

  return (
    <Dialog open={open} onOpenChange={resetOnClose(onOpenChange)}>
      <DialogContent closeLabel="Schließen" role="alertdialog">
        <DialogHeading title={title} description={description} />
        <FormError message={error} />
        <DialogFooter showCloseButton closeLabel="Abbrechen">
          <Button
            variant="destructive"
            disabled={isBusy}
            onClick={() => void run(onConfirm, () => onOpenChange(false))}
          >
            {isBusy ? "Löscht …" : confirmLabel}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
