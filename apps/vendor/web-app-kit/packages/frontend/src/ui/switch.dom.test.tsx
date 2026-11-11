import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { Label, Switch } = await import("./index.js");

test.afterEach(cleanup);

test("Space and click toggle the switch and report aria-checked", async () => {
  const user = userEvent.setup();
  const changes: boolean[] = [];
  render(
    <>
      <Switch id="autosave" onCheckedChange={(checked) => changes.push(checked)} />
      <Label htmlFor="autosave">Automatisch speichern</Label>
    </>,
  );
  const control = screen.getByRole("switch", { name: "Automatisch speichern" });
  assert.equal(control.getAttribute("aria-checked"), "false");
  await user.tab();
  assert.equal(document.activeElement, control);
  await user.keyboard(" ");
  assert.equal(control.getAttribute("aria-checked"), "true");
  assert.equal(control.dataset.state, "checked");
  await user.click(control);
  assert.equal(control.dataset.state, "unchecked");
  assert.deepEqual(changes, [true, false]);
});

test("disabled switch ignores keyboard and pointer", async () => {
  const user = userEvent.setup();
  render(<Switch aria-label="Gesperrt" disabled />);
  const control = screen.getByRole("switch", { name: "Gesperrt" });
  assert.equal(control.hasAttribute("disabled"), true);
  await user.click(control);
  control.focus();
  await user.keyboard(" ");
  assert.equal(control.getAttribute("aria-checked"), "false");
});

const STATE_CLASSES = [
  "focus-visible:outline-2",
  "focus-visible:outline-offset-2",
  "focus-visible:outline-solid",
  "focus-visible:outline-ring",
  "data-checked:bg-primary",
  "data-unchecked:bg-input",
  "data-unchecked:not-disabled:hover:border-primary",
  "data-unchecked:not-disabled:active:inset-ring-primary",
  "data-checked:not-disabled:hover:bg-primary-hover",
  "data-checked:not-disabled:active:bg-primary-active",
  "data-disabled:cursor-not-allowed",
  "data-disabled:opacity-50",
];

test("switch carries hover, pressed, focus, checked and disabled classes", () => {
  render(<Switch aria-label="Zustände" size="sm" />);
  const control = screen.getByRole("switch", { name: "Zustände" });
  const classes = control.className.split(" ");
  for (const token of STATE_CLASSES) assert.ok(classes.includes(token), token);
  assert.equal(classes.includes("focus-visible:ring-ring/50"), false);
  assert.ok(classes.includes("dark:data-unchecked:bg-input"));
  assert.equal(classes.includes("dark:data-unchecked:bg-input/80"), false);
  assert.equal(classes.some((name) => /hover:bg-(muted|accent)/.test(name)), false);
  assert.equal(control.dataset.size, "sm");
});
