import { Button } from "@web-app-kit/frontend/ui";
import { FormError } from "@/components/common";
import type { FormActionsProps } from "./entries.types";

export function FormActions({ isBusy, error, onCancel }: FormActionsProps) {
  return (
    <>
      <FormError message={error} />
      <div className="flex gap-2">
        <Button type="submit" disabled={isBusy}>
          {isBusy ? "Speichert …" : "Speichern"}
        </Button>
        <Button type="button" variant="outline" onClick={onCancel}>
          Abbrechen
        </Button>
      </div>
    </>
  );
}
