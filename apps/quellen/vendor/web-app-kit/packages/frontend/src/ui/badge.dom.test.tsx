import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { Badge } = await import("./index.js");

test.afterEach(cleanup);

const TONE_CLASSES = {
  destructive: [
    "bg-destructive/10",
    "text-destructive",
    "dark:bg-destructive/20",
    "[a]:hover:bg-destructive/20",
    "dark:[a]:hover:bg-destructive/25",
  ],
  info: [
    "bg-info/10",
    "text-info",
    "dark:bg-info/20",
    "[a]:hover:bg-info/20",
    "dark:[a]:hover:bg-info/25",
  ],
  warning: [
    "bg-warning/10",
    "text-warning",
    "dark:bg-warning/20",
    "[a]:hover:bg-warning/20",
    "dark:[a]:hover:bg-warning/25",
  ],
} as const;

for (const [variant, expectedClasses] of Object.entries(TONE_CLASSES)) {
  test(`${variant} badge carries tinted surface and role text classes`, () => {
    render(<Badge variant={variant as keyof typeof TONE_CLASSES}>Status</Badge>);
    const badge = screen.getByText("Status");
    const classes = badge.className.split(" ");
    assert.equal(badge.getAttribute("data-variant"), variant);
    for (const expected of expectedClasses) assert.ok(classes.includes(expected), expected);
    assert.equal(classes.some((name) => /hover:bg-(muted|accent)/.test(name)), false);
  });
}

test("tone badge as link keeps link semantics and focus classes", () => {
  render(
    <Badge asChild variant="info">
      <a href="#release">Version 1.3.0</a>
    </Badge>,
  );
  const link = screen.getByRole("link", { name: "Version 1.3.0" });
  const classes = link.className.split(" ");
  assert.ok(classes.includes("[a]:hover:bg-info/20"));
  assert.ok(classes.includes("dark:[a]:hover:bg-info/25"));
  assert.ok(classes.includes("focus-visible:ring-ring/50"));
  assert.ok(classes.includes("[a]:active:translate-y-px"));
});
