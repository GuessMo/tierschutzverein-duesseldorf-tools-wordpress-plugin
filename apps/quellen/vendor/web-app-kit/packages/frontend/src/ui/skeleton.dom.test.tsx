import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render } = await import("@testing-library/react");
const { Skeleton } = await import("./index.js");

test.afterEach(cleanup);

test("skeleton is decorative and hidden from assistive technology", () => {
  const { container } = render(<Skeleton className="h-4 w-32" />);
  const skeleton = container.querySelector<HTMLElement>("[data-slot=skeleton]");
  assert.equal(skeleton?.getAttribute("aria-hidden"), "true");
  assert.equal(skeleton?.getAttribute("role"), null);
});

test("skeleton pulses only without reduced motion and uses a neutral surface", () => {
  const { container } = render(<Skeleton />);
  const classes = container.querySelector("[data-slot=skeleton]")?.className.split(" ") ?? [];
  assert.ok(classes.includes("animate-pulse"));
  assert.ok(classes.includes("motion-reduce:animate-none"));
  assert.ok(classes.includes("bg-accent"));
  assert.equal(classes.includes("bg-muted"), false);
});
