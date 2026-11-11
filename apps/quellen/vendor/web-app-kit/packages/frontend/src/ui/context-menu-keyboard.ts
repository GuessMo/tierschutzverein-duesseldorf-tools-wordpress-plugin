import type { KeyboardEvent } from "react";

function isContextMenuKey(event: KeyboardEvent<HTMLElement>): boolean {
  return event.key === "ContextMenu" || (event.shiftKey && event.key === "F10");
}

export function openContextMenuFromKeyboard(event: KeyboardEvent<HTMLElement>): void {
  if (!isContextMenuKey(event)) return;
  event.preventDefault();
  const target = event.currentTarget;
  const { left, bottom } = target.getBoundingClientRect();
  const init = { bubbles: true, cancelable: true, clientX: left, clientY: bottom };
  target.dispatchEvent(new MouseEvent("contextmenu", init));
}
