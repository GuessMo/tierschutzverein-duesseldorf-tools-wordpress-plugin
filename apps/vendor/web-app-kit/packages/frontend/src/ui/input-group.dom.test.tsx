import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const ui = await import("./index.js");

test.afterEach(cleanup);

test("clicking the addon focuses the input of the group", async () => {
  const user = userEvent.setup();
  render(
    <ui.InputGroup>
      <ui.InputGroupInput aria-label="Suche" />
      <ui.InputGroupAddon>
        <ui.InputGroupText>@</ui.InputGroupText>
      </ui.InputGroupAddon>
    </ui.InputGroup>,
  );
  await user.click(screen.getByText("@"));
  assert.equal(document.activeElement, screen.getByRole("textbox", { name: "Suche" }));
});

test("a button in the addon keeps its own click", async () => {
  const user = userEvent.setup();
  let clicks = 0;
  render(
    <ui.InputGroup>
      <ui.InputGroupInput aria-label="Suche" />
      <ui.InputGroupAddon align="inline-end">
        <ui.InputGroupButton aria-label="Leeren" onClick={() => (clicks += 1)}>
          x
        </ui.InputGroupButton>
      </ui.InputGroupAddon>
    </ui.InputGroup>,
  );
  const button = screen.getByRole("button", { name: "Leeren" });
  await user.click(button);
  assert.equal(clicks, 1);
  assert.equal(document.activeElement, button);
  assert.equal(button.getAttribute("type"), "button");
});

test("the group shows focus and invalid state of its control", () => {
  render(
    <ui.InputGroup data-testid="group">
      <ui.InputGroupTextarea aria-label="Notiz" aria-invalid />
    </ui.InputGroup>,
  );
  const group = screen.getByTestId("group");
  assert.equal(group.getAttribute("role"), "group");
  for (const name of [
    "has-[[data-slot=input-group-control]:focus-visible]:border-ring",
    "has-[[data-slot][aria-invalid=true]]:border-destructive",
    "has-disabled:opacity-50",
  ]) {
    assert.ok(group.className.includes(name), name);
  }
  const control = screen.getByRole("textbox", { name: "Notiz" });
  assert.equal(control.dataset.slot, "input-group-control");
});
