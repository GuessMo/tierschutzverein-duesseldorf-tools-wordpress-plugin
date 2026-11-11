import { useEffect } from "react";

const OPEN_POPOVER = "[data-radix-popper-content-wrapper], [role='dialog'], [role='alertdialog']";

export function useEscape(onEscape: () => void) {
  useEffect(() => {
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key !== "Escape" || event.defaultPrevented) return;
      if (document.querySelector(OPEN_POPOVER)) return;
      onEscape();
    };
    document.addEventListener("keydown", onKeyDown);
    return () => document.removeEventListener("keydown", onKeyDown);
  }, [onEscape]);
}
