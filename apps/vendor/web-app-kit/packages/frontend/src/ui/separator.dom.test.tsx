import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { Separator } = await import("./index.js");

test.afterEach(cleanup);

test("separator is decorative and horizontal by default", () => {
  const { container } = render(<Separator />);
  const separator = container.querySelector("[data-slot=separator]");
  assert.equal(separator?.getAttribute("role"), "none");
  assert.equal(separator?.getAttribute("data-orientation"), "horizontal");
  assert.equal(screen.queryByRole("separator"), null);
  assert.ok(separator?.className.includes("data-horizontal:h-px"));
});

test("semantic vertical separator exposes role and aria-orientation", () => {
  render(<Separator orientation="vertical" decorative={false} />);
  const separator = screen.getByRole("separator");
  assert.equal(separator.getAttribute("aria-orientation"), "vertical");
  assert.equal(separator.getAttribute("data-orientation"), "vertical");
  assert.ok(separator.className.includes("data-vertical:w-px"));
});

test("semantic horizontal separator omits aria-orientation", () => {
  render(<Separator decorative={false} />);
  const separator = screen.getByRole("separator");
  assert.equal(separator.getAttribute("aria-orientation"), null);
});
