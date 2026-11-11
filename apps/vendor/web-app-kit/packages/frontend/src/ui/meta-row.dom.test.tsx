import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { Badge, MetaRow } = await import("./index.js");

test.afterEach(cleanup);

const ROW_CLASSES = ["flex", "flex-wrap", "items-center", "gap-2", "text-sm"];

test("MetaRow with label is a named group with a visible bold label", () => {
  render(
    <MetaRow label="Status">
      <Badge variant="info">Beta</Badge>
      <Badge variant="warning">Veraltet</Badge>
    </MetaRow>,
  );
  const group = screen.getByRole("group", { name: "Status" });
  const label = screen.getByText("Status");
  assert.equal(label.parentElement, group);
  assert.ok(label.className.split(" ").includes("font-semibold"));
  assert.equal(group.textContent, "StatusBetaVeraltet");
  for (const expected of ROW_CLASSES) assert.ok(group.className.split(" ").includes(expected));
});

test("MetaRow without label renders a plain wrapping row", () => {
  const { container } = render(
    <MetaRow className="mt-2">
      <Badge>v1.3.0</Badge>
    </MetaRow>,
  );
  const row = container.querySelector("[data-slot=meta-row]");
  assert.equal(row?.getAttribute("role"), null);
  assert.equal(row?.getAttribute("aria-label"), null);
  assert.equal(screen.queryByRole("group"), null);
  assert.equal(row?.textContent, "v1.3.0");
  const classes = row?.className.split(" ") ?? [];
  for (const expected of [...ROW_CLASSES, "mt-2"]) assert.ok(classes.includes(expected));
});
