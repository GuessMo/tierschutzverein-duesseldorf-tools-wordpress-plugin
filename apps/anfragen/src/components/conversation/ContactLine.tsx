import { useState } from "react";
import { IconCheck, IconCopy } from "@tabler/icons-react";
import { Button, toast } from "@web-app-kit/frontend/ui";

const RESET_MS = 1600;

export function ContactLine({ label, value, href }: { label: string; value: string; href: string }) {
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
    <span className="inline-flex items-center gap-1 text-sm">
      <a
        href={href}
        className="rounded-sm underline-offset-4 hover:underline focus-visible:underline focus-visible:outline-2 focus-visible:outline-ring active:text-primary"
      >
        {value}
      </a>
      <Button
        type="button"
        variant="ghost"
        size="icon-xs"
        aria-label={isCopied ? `${label} kopiert` : `${label} kopieren`}
        onClick={() => void copy()}
      >
        {isCopied ? <IconCheck aria-hidden /> : <IconCopy aria-hidden />}
      </Button>
    </span>
  );
}
