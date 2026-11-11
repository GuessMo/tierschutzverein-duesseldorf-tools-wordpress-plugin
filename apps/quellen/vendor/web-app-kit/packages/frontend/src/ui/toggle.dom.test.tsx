import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { Toggle } = await import("./index.js");

test.afterEach(cleanup);

const STATE_CLASSES = [
  "data-[state=on]:bg-primary",
  "data-[state=on]:text-primary-foreground",
  "data-[state=on]:not-disabled:hover:bg-primary-hover",
  "data-[state=on]:not-disabled:active:bg-primary-active",
  "not-disabled:hover:bg-primary-subtle",
  "not-disabled:hover:text-primary-subtle-foreground",
  "data-[state=off]:not-disabled:active:border-primary",
  "data-[state=off]:not-disabled:active:inset-ring-1",
  "data-[state=off]:not-disabled:active:inset-ring-primary",
  "focus-visible:ring-3",
  "focus-visible:ring-ring/50",
  "not-disabled:active:translate-y-px",
  "disabled:cursor-not-allowed",
  "disabled:opacity-50",
];

test("Toggle carries active, hover, focus, pressed and disabled classes", () => {
  render(<Toggle aria-label="Rahmen" />);
  const toggle = screen.getByRole("button", { name: "Rahmen" });
  const classes = toggle.className.split(" ");
  for (const expected of STATE_CLASSES) assert.ok(classes.includes(expected), expected);
  assert.equal(classes.some((name) => /hover:(bg-(muted|accent)|overlay-hover)/.test(name)), false);
});

test("Toggle switches aria-pressed and data-state on click", async () => {
  const user = userEvent.setup();
  const changes: boolean[] = [];
  render(<Toggle aria-label="Raster" onPressedChange={(pressed) => changes.push(pressed)} />);
  const toggle = screen.getByRole("button", { name: "Raster" });
  assert.equal(toggle.getAttribute("aria-pressed"), "false");
  assert.equal(toggle.dataset.state, "off");
  await user.click(toggle);
  assert.equal(toggle.getAttribute("aria-pressed"), "true");
  assert.equal(toggle.dataset.state, "on");
  await user.click(toggle);
  assert.equal(toggle.getAttribute("aria-pressed"), "false");
  assert.deepEqual(changes, [true, false]);
});

test("Toggle forwards variant and size, outline marks the active border", () => {
  render(<Toggle aria-label="Lineal" variant="outline" size="icon-sm" defaultPressed />);
  const toggle = screen.getByRole("button", { name: "Lineal" });
  assert.equal(toggle.dataset.variant, "outline");
  assert.equal(toggle.dataset.size, "icon-sm");
  assert.equal(toggle.getAttribute("aria-pressed"), "true");
  assert.ok(toggle.className.includes("data-[state=on]:border-primary"));
  assert.ok(toggle.className.includes("size-7"));
});

test("disabled Toggle ignores clicks", async () => {
  const user = userEvent.setup();
  render(<Toggle aria-label="Sperre" disabled />);
  const toggle = screen.getByRole("button", { name: "Sperre" });
  await user.click(toggle);
  assert.equal(toggle.getAttribute("aria-pressed"), "false");
});
