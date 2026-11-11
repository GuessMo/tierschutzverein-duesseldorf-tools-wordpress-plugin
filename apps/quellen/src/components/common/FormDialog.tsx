import type { FormEvent } from "react";
import { Button, Dialog, DialogContent, DialogFooter } from "@web-app-kit/frontend/ui";
import type { FormDialogProps } from "./common.types";
import { DialogHeading } from "./DialogHeading";
import { FormError } from "./FormError";
import { useSubmitState } from "./useSubmitState";

export function FormDialog(props: FormDialogProps) {
  const { open, onOpenChange, title, description, submitLabel, onSubmit, children } = props;
  const { isBusy, error, run, resetOnClose } = useSubmitState();
  const handleSubmit = (event: FormEvent) => {
    event.preventDefault();
    void run(onSubmit, () => onOpenChange(false));
  };

  return (
    <Dialog open={open} onOpenChange={resetOnClose(onOpenChange)}>
      <DialogContent closeLabel="Schließen">
        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
          <DialogHeading title={title} description={description} />
          <div className="flex flex-col gap-4">{children}</div>
          <FormError message={error} />
          <DialogFooter showCloseButton closeLabel="Abbrechen">
            <Button type="submit" disabled={isBusy}>
              {isBusy ? "Speichert …" : submitLabel}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
