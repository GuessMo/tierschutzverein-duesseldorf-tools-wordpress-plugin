import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { Progress } = await import("./index.js");

test.afterEach(cleanup);

test("progress exposes role, name and aria values", () => {
  render(<Progress aria-label="Upload" value={40} />);
  const bar = screen.getByRole("progressbar", { name: "Upload" });
  assert.equal(bar.getAttribute("aria-valuemin"), "0");
  assert.equal(bar.getAttribute("aria-valuemax"), "100");
  assert.equal(bar.getAttribute("aria-valuenow"), "40");
  assert.equal(bar.dataset.state, "loading");
});

test("indicator position follows value relative to max", () => {
  render(<Progress aria-label="Import" value={50} max={200} />);
  const bar = screen.getByRole("progressbar", { name: "Import" });
  const indicator = bar.querySelector<HTMLElement>("[data-slot=progress-indicator]");
  assert.equal(indicator?.style.transform, "translateX(-75%)");
  assert.equal(bar.getAttribute("aria-valuemax"), "200");
});

test("missing value renders an indeterminate bar without aria-valuenow", () => {
  render(<Progress aria-label="Warten" value={null} />);
  const bar = screen.getByRole("progressbar", { name: "Warten" });
  assert.equal(bar.hasAttribute("aria-valuenow"), false);
  assert.equal(bar.dataset.state, "indeterminate");
});

test("indicator uses the primary role and stops moving with reduced motion", () => {
  render(<Progress aria-label="Klassen" value={10} />);
  const bar = screen.getByRole("progressbar", { name: "Klassen" });
  const indicator = bar.querySelector("[data-slot=progress-indicator]");
  const classes = indicator?.className.split(" ") ?? [];
  assert.ok(classes.includes("bg-primary"));
  assert.ok(classes.includes("motion-reduce:transition-none"));
  assert.ok(bar.className.split(" ").includes("bg-muted"));
});
