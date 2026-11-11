import { useState } from "react";
import { describeError } from "@web-app-kit/frontend";

const SAVE_ERROR = "Speichern ist fehlgeschlagen. Bitte versuche es erneut.";

export function useSubmitState() {
  const [isBusy, setIsBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const run = async (action: () => Promise<void>, onDone: () => void) => {
    setIsBusy(true);
    setError(null);
    try {
      await action();
      onDone();
    } catch (caught) {
      setError(describeError(caught, SAVE_ERROR));
    } finally {
      setIsBusy(false);
    }
  };

  const resetOnClose = (onOpenChange: (open: boolean) => void) => (open: boolean) => {
    if (!open) setError(null);
    onOpenChange(open);
  };

  return { isBusy, error, run, resetOnClose };
}
