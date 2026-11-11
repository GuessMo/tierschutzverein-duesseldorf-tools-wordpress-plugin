import { APP_SHELL_CONTENT_ID } from "@web-app-kit/frontend/ui";

const NAVIGATION_EVENT = "catalog:navigate";

export function subscribeToPath(onChange: () => void): () => void {
  window.addEventListener("popstate", onChange);
  window.addEventListener(NAVIGATION_EVENT, onChange);
  return () => {
    window.removeEventListener("popstate", onChange);
    window.removeEventListener(NAVIGATION_EVENT, onChange);
  };
}

export function readPath(): string {
  return window.location.pathname;
}

function focusContent() {
  const content = document.getElementById(APP_SHELL_CONTENT_ID);
  if (!content) return;
  content.scrollTop = 0;
  content.focus({ preventScroll: true });
}

export function navigate(path: string) {
  if (path === readPath()) return;
  window.history.pushState(null, "", path);
  window.dispatchEvent(new Event(NAVIGATION_EVENT));
  focusContent();
}
