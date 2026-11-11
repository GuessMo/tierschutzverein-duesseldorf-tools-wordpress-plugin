import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, fireEvent, render, screen } = await import("@testing-library/react");
const { Dialog, DialogContent, DialogFooter, DialogTitle } = await import("./index.js");

test.afterEach(cleanup);

test("DialogContent renders the app-provided close label in the portal", () => {
  render(
    <Dialog open>
      <DialogContent closeLabel="Schliessen">
        <DialogTitle>Titel</DialogTitle>
      </DialogContent>
    </Dialog>,
  );
  const close = screen.getByRole("button", { name: "Schliessen" });
  assert.equal(close.closest("[data-slot=dialog-content]") !== null, true);
  assert.equal(document.body.textContent?.includes("Close"), false);
});

test("DialogFooter close button uses closeLabel and closes the dialog", () => {
  let isOpen = true;
  render(
    <Dialog open onOpenChange={(next) => (isOpen = next)}>
      <DialogContent showCloseButton={false}>
        <DialogTitle>Titel</DialogTitle>
        <DialogFooter showCloseButton closeLabel="Abbrechen" />
      </DialogContent>
    </Dialog>,
  );
  fireEvent.click(screen.getByRole("button", { name: "Abbrechen" }));
  assert.equal(isOpen, false);
});

test("Escape requests closing", () => {
  let isOpen = true;
  render(
    <Dialog open onOpenChange={(next) => (isOpen = next)}>
      <DialogContent closeLabel="Close dialog">
        <DialogTitle>Titel</DialogTitle>
      </DialogContent>
    </Dialog>,
  );
  fireEvent.keyDown(document.activeElement ?? document.body, { key: "Escape" });
  assert.equal(isOpen, false);
});
