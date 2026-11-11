import { useState } from "react";
import { IconCheck, IconCopy } from "@tabler/icons-react";
import { Button, toast } from "@web-app-kit/frontend/ui";
import type { CopyValueProps } from "./common.types";

const RESET_MS = 1600;

export function CopyValue({ label, value }: CopyValueProps) {
  const [isCopied, setIsCopied] = useState(false);
  const copy = async () => {
    try {
      await navigator.clipboard.writeText(value);
      setIsCopied(true);
      window.setTimeout(() => setIsCopied(false), RESET_MS);
    } catch {
      toast.error(`${label} konnte nicht kopiert werden.`);
    }
  };
  return (
    <span className="inline-flex items-center gap-1 text-sm text-muted-foreground">
      <span>{label}</span>
      <span className="font-mono text-xs text-foreground tabular-nums">{value}</span>
      <Button
        type="button"
        variant="ghost"
        size="icon-sm"
        aria-label={isCopied ? `${label} kopiert` : `${label} kopieren`}
        onClick={() => void copy()}
      >
        {isCopied ? <IconCheck aria-hidden /> : <IconCopy aria-hidden />}
      </Button>
    </span>
  );
}
