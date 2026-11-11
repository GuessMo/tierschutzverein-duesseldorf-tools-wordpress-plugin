import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen, waitFor } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const ui = await import("./index.js");

test.afterEach(cleanup);

function renderAlertDialog(onConfirm: () => void = () => {}) {
  render(
    <ui.AlertDialog>
      <ui.AlertDialogTrigger asChild>
        <ui.Button variant="outline">Projekt löschen</ui.Button>
      </ui.AlertDialogTrigger>
      <ui.AlertDialogContent>
        <ui.AlertDialogHeader>
          <ui.AlertDialogTitle>Projekt löschen?</ui.AlertDialogTitle>
          <ui.AlertDialogDescription>Alle Entwürfe gehen verloren.</ui.AlertDialogDescription>
        </ui.AlertDialogHeader>
        <ui.AlertDialogFooter>
          <ui.AlertDialogCancel>Behalten</ui.AlertDialogCancel>
          <ui.AlertDialogAction variant="destructive" onClick={onConfirm}>
            Endgültig löschen
          </ui.AlertDialogAction>
        </ui.AlertDialogFooter>
      </ui.AlertDialogContent>
    </ui.AlertDialog>,
  );
  return screen.getByRole("button", { name: "Projekt löschen" });
}

test("opens as alertdialog with title, description and focus on cancel", async () => {
  const user = userEvent.setup();
  await user.click(renderAlertDialog());
  const dialog = await screen.findByRole("alertdialog", { name: "Projekt löschen?" });
  assert.equal(dialog.getAttribute("aria-describedby") !== null, true);
  assert.equal(document.activeElement, screen.getByRole("button", { name: "Behalten" }));
});

test("Escape closes and returns focus to the trigger", async () => {
  const user = userEvent.setup();
  const trigger = renderAlertDialog();
  await user.click(trigger);
  await screen.findByRole("alertdialog");
  await user.keyboard("{Escape}");
  await waitFor(() => assert.equal(screen.queryByRole("alertdialog"), null));
  assert.equal(document.activeElement, trigger);
});

test("action runs the callback, closes and uses the destructive variant", async () => {
  const user = userEvent.setup();
  let confirmations = 0;
  await user.click(renderAlertDialog(() => (confirmations += 1)));
  const action = await screen.findByRole("button", { name: "Endgültig löschen" });
  assert.equal(action.dataset.variant, "destructive");
  assert.equal(action.dataset.slot, "alert-dialog-action");
  await user.click(action);
  assert.equal(confirmations, 1);
  await waitFor(() => assert.equal(screen.queryByRole("alertdialog"), null));
});

test("cancel is an outline button and no built-in English text appears", async () => {
  const user = userEvent.setup();
  await user.click(renderAlertDialog());
  const cancel = await screen.findByRole("button", { name: "Behalten" });
  assert.equal(cancel.dataset.variant, "outline");
  assert.equal(/\b(Cancel|Continue|OK)\b/.test(document.body.textContent ?? ""), false);
});
