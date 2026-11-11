import { navigate } from "./navigation";

function isPlainClick(event: MouseEvent): boolean {
  if (event.defaultPrevented || event.button !== 0) return false;
  return !(event.metaKey || event.ctrlKey || event.shiftKey || event.altKey);
}

function internalHref(target: EventTarget | null): string | null {
  if (!(target instanceof Element)) return null;
  const anchor = target.closest("a[href]");
  if (!anchor || anchor.hasAttribute("target") || anchor.hasAttribute("download")) return null;
  const href = anchor.getAttribute("href");
  return href?.startsWith("/") ? href : null;
}

export function interceptLinkClicks(): () => void {
  const onClick = (event: MouseEvent) => {
    const href = internalHref(event.target);
    if (!href || !isPlainClick(event)) return;
    event.preventDefault();
    navigate(href);
  };
  document.addEventListener("click", onClick);
  return () => document.removeEventListener("click", onClick);
}
