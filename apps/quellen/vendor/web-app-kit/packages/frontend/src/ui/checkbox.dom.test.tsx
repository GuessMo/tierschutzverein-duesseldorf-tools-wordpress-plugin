import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { Checkbox, Label } = await import("./index.js");

test.afterEach(cleanup);

test("Space toggles the focused checkbox on and off", async () => {
  const user = userEvent.setup();
  const changes: unknown[] = [];
  render(
    <>
      <Checkbox id="grid" onCheckedChange={(state) => changes.push(state)} />
      <Label htmlFor="grid">Raster</Label>
    </>,
  );
  const checkbox = screen.getByRole("checkbox", { name: "Raster" });
  await user.tab();
  assert.equal(document.activeElement, checkbox);
  assert.equal(checkbox.getAttribute("aria-checked"), "false");
  await user.keyboard(" ");
  assert.equal(checkbox.getAttribute("aria-checked"), "true");
  assert.equal(checkbox.dataset.state, "checked");
  await user.keyboard(" ");
  assert.equal(checkbox.getAttribute("aria-checked"), "false");
  assert.deepEqual(changes, [true, false]);
});

test("indeterminate shows aria-checked mixed and Space resolves it to checked", async () => {
  const user = userEvent.setup();
  render(<Checkbox aria-label="Alle" defaultChecked="indeterminate" />);
  const checkbox = screen.getByRole("checkbox", { name: "Alle" });
  assert.equal(checkbox.getAttribute("aria-checked"), "mixed");
  assert.equal(checkbox.dataset.state, "indeterminate");
  const icons = checkbox.querySelectorAll("[data-slot=checkbox-indicator] svg");
  assert.equal(icons.length, 2);
  const minusClasses = icons[1].getAttribute("class") ?? "";
  assert.ok(minusClasses.includes("group-data-[state=indeterminate]/checkbox:block"));
  await user.tab();
  await user.keyboard(" ");
  assert.equal(checkbox.getAttribute("aria-checked"), "true");
});

test("disabled checkbox ignores keyboard and pointer", async () => {
  const user = userEvent.setup();
  render(<Checkbox aria-label="Sperre" disabled />);
  const checkbox = screen.getByRole("checkbox", { name: "Sperre" });
  await user.click(checkbox);
  checkbox.focus();
  await user.keyboard(" ");
  assert.equal(checkbox.getAttribute("aria-checked"), "false");
});

const STATE_CLASSES = [
  "focus-visible:outline-2",
  "focus-visible:outline-offset-2",
  "focus-visible:outline-solid",
  "focus-visible:outline-ring",
  "data-unchecked:not-disabled:hover:bg-primary-subtle",
  "data-unchecked:not-disabled:active:inset-ring-primary",
  "data-checked:bg-primary",
  "data-checked:not-disabled:hover:bg-primary-hover",
  "data-checked:not-disabled:active:bg-primary-active",
  "data-[state=indeterminate]:bg-primary",
  "disabled:cursor-not-allowed",
  "disabled:opacity-50",
];

test("checkbox carries hover, pressed, focus, checked and disabled classes", () => {
  render(<Checkbox aria-label="Zustände" />);
  const classes = screen.getByRole("checkbox", { name: "Zustände" }).className.split(" ");
  for (const token of STATE_CLASSES) assert.ok(classes.includes(token), token);
  assert.equal(classes.includes("focus-visible:ring-ring/50"), false);
});
