import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen, waitFor } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { ToggleGroup, ToggleGroupItem } = await import("./index.js");

test.afterEach(cleanup);

function ToolItems() {
  return (
    <>
      <ToggleGroupItem value="select" aria-label="Auswahl" />
      <ToggleGroupItem value="frame" aria-label="Rahmen" />
      <ToggleGroupItem value="text" aria-label="Text" />
    </>
  );
}

test("single ToggleGroup exposes the label and one active item", async () => {
  const user = userEvent.setup();
  const values: string[] = [];
  render(
    <ToggleGroup
      type="single"
      label="Werkzeuge"
      defaultValue="select"
      onValueChange={(value) => values.push(value)}
    >
      <ToolItems />
    </ToggleGroup>,
  );
  const group = screen.getByRole("radiogroup", { name: "Werkzeuge" });
  assert.equal(group.dataset.slot, "toggle-group");
  const frame = screen.getByRole("radio", { name: "Rahmen" });
  await user.click(frame);
  assert.equal(frame.getAttribute("aria-checked"), "true");
  assert.equal(frame.dataset.state, "on");
  assert.equal(screen.getByRole("radio", { name: "Auswahl" }).dataset.state, "off");
  assert.ok(frame.className.includes("data-[state=on]:bg-primary"));
  assert.deepEqual(values, ["frame"]);
});

test("arrow keys move focus between items with roving tabindex", async () => {
  const user = userEvent.setup();
  render(
    <ToggleGroup type="single" label="Werkzeuge" defaultValue="select">
      <ToolItems />
    </ToggleGroup>,
  );
  await user.tab();
  const select = screen.getByRole("radio", { name: "Auswahl" });
  assert.equal(document.activeElement, select);
  await user.keyboard("{ArrowRight}");
  await waitFor(() => assert.equal(document.activeElement?.getAttribute("aria-label"), "Rahmen"));
  await user.keyboard("{ArrowLeft}");
  await waitFor(() => assert.equal(document.activeElement, select));
  assert.equal(screen.getByRole("radio", { name: "Rahmen" }).getAttribute("tabindex"), "-1");
});

test("multiple ToggleGroup toggles items independently with aria-pressed", async () => {
  const user = userEvent.setup();
  render(
    <>
      <h2 id="view-heading">Ansicht</h2>
      <ToggleGroup type="multiple" aria-labelledby="view-heading" variant="outline" size="icon">
        <ToggleGroupItem value="grid" aria-label="Raster" />
        <ToggleGroupItem value="ruler" aria-label="Lineal" />
      </ToggleGroup>
    </>,
  );
  assert.ok(screen.getByRole("toolbar", { name: "Ansicht" }));
  const grid = screen.getByRole("button", { name: "Raster" });
  const ruler = screen.getByRole("button", { name: "Lineal" });
  await user.click(grid);
  await user.click(ruler);
  assert.equal(grid.getAttribute("aria-pressed"), "true");
  assert.equal(ruler.getAttribute("aria-pressed"), "true");
  await user.click(grid);
  assert.equal(grid.getAttribute("aria-pressed"), "false");
  assert.equal(grid.dataset.variant, "outline");
  assert.equal(grid.dataset.size, "icon");
  grid.focus();
  await user.keyboard("{ArrowRight}");
  await waitFor(() => assert.equal(document.activeElement, ruler));
});
