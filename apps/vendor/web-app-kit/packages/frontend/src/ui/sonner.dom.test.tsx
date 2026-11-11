import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();
Object.assign(window, {
  matchMedia: () => ({ matches: false, addEventListener() {}, removeEventListener() {} }),
});
Object.assign(HTMLElement.prototype, {
  setPointerCapture() {},
  releasePointerCapture() {},
  hasPointerCapture: () => false,
});

const { act, cleanup, fireEvent, render, screen } = await import("@testing-library/react");
const { ThemeProvider } = await import("../ThemeProvider.js");
const { Toaster, toast } = await import("./index.js");

const UNMOUNT_DELAY_MS = 400;

const CLOSE_OVERRIDE_CLASSES = [
  "bg-transparent!",
  "border-transparent!",
  "not-disabled:hover:bg-primary-subtle!",
  "not-disabled:active:border-primary!",
  "focus-visible:border-ring!",
];

const TOAST_FOCUS_CLASSES = [
  "focus-visible:outline-2!",
  "focus-visible:outline-offset-2!",
  "focus-visible:outline-ring!",
];

test.afterEach(() => {
  act(() => {
    toast.dismiss();
  });
  cleanup();
});

function renderToaster(theme: "light" | "dark" = "light") {
  render(
    <ThemeProvider defaultTheme={theme} storageKey="toast-test">
      <Toaster closeLabel="Hinweis schließen" regionLabel="Benachrichtigungen" />
    </ThemeProvider>,
  );
}

test("live region exists before the first toast and carries the region label", () => {
  renderToaster();
  const region = screen.getByRole("region", { name: /^Benachrichtigungen/ });
  assert.equal(region.getAttribute("aria-live"), "polite");
  assert.equal(document.body.textContent?.includes("Notifications"), false);
});

test("toast shows title, description, Tabler icon and a labelled close button", async () => {
  renderToaster();
  act(() => {
    toast.success("Gespeichert", { description: "Alle Änderungen sind übernommen." });
  });
  const item = await screen.findByText("Gespeichert");
  const toastElement = item.closest<HTMLElement>("[data-sonner-toast]");
  assert.ok(toastElement);
  assert.equal(toastElement.dataset.styled, "false");
  assert.ok(toastElement.querySelector("[data-icon] svg.tabler-icon-circle-check"));
  const close = screen.getByRole("button", { name: "Hinweis schließen" });
  const closeClasses = close.className.split(" ");
  for (const token of CLOSE_OVERRIDE_CLASSES) assert.ok(closeClasses.includes(token), token);
  const toastClasses = toastElement.className.split(" ");
  for (const token of TOAST_FOCUS_CLASSES) assert.ok(toastClasses.includes(token), token);
  assert.ok(screen.getByText("Alle Änderungen sind übernommen."));
});

test("close button dismisses the toast", async () => {
  renderToaster();
  act(() => {
    toast("Kopiert");
  });
  await screen.findByText("Kopiert");
  fireEvent.click(screen.getByRole("button", { name: "Hinweis schließen" }));
  await act(() => new Promise((resolve) => setTimeout(resolve, UNMOUNT_DELAY_MS)));
  assert.equal(screen.queryByText("Kopiert"), null);
});

test("action button uses kit button classes and runs its callback", async () => {
  let undone = 0;
  renderToaster();
  act(() => {
    toast("Gelöscht", { action: { label: "Rückgängig", onClick: () => (undone += 1) } });
  });
  const action = await screen.findByRole("button", { name: "Rückgängig" });
  assert.ok(action.className.split(" ").includes("bg-primary"));
  fireEvent.click(action);
  assert.equal(undone, 1);
});

test("toaster follows the resolved theme of the ThemeProvider", async () => {
  renderToaster("dark");
  act(() => {
    toast("Dunkel");
  });
  await screen.findByText("Dunkel");
  const toaster = document.querySelector<HTMLElement>("[data-sonner-toaster]");
  assert.equal(toaster?.dataset.sonnerTheme, "dark");
});
