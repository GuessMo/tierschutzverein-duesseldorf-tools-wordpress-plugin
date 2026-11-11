import { useCallback, useState } from "react";
import type { AskConfirmation, PendingConfirmation } from "@/types";

export function useConfirm() {
  const [pending, setPending] = useState<PendingConfirmation | null>(null);

  const ask = useCallback<AskConfirmation>((confirmation, run) => {
    setPending({ confirmation, run });
  }, []);

  const cancel = useCallback(() => setPending(null), []);

  const confirm = useCallback(() => {
    setPending(null);
    void pending?.run();
  }, [pending]);

  return { confirmation: pending?.confirmation ?? null, ask, cancel, confirm };
}
