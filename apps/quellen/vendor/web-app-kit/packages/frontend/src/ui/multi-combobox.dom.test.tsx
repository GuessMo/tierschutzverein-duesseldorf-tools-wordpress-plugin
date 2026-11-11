import assert from "node:assert/strict";
import { test } from "node:test";
import { useState } from "react";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

class NoResizeObserver {
  observe() {}
  unobserve() {}
  disconnect() {}
}
Object.assign(globalThis, { ResizeObserver: NoResizeObserver });
Element.prototype.scrollIntoView = () => {};

const { cleanup, render, screen } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { Label } = await import("./index.js");
const { MultiCombobox } = await import("./command-entry.js");

test.afterEach(cleanup);

const OPTIONS = [
  { value: "1", label: "Ersatzteile" },
  { value: "2", label: "Gasgrills" },
  { value: "3", label: "Smoker" },
];

function Harness({ initial = [], disabled = false }: { initial?: string[]; disabled?: boolean }) {
  const [value, setValue] = useState<string[]>(initial);
  return (
    <>
      <Label htmlFor="categories">Kategorien</Label>
      <MultiCombobox
        id="categories"
        options={OPTIONS}
        value={value}
        onValueChange={setValue}
        placeholder="Kategorie wählen"
        searchPlaceholder="Suchen"
        emptyText="Nichts gefunden"
        selectedLabel="Ausgewählt"
        removeLabel={(label) => `${label} entfernen`}
        disabled={disabled}
      />
      <output data-testid="value">{value.join(",")}</output>
    </>
  );
}

function trigger() {
  return screen.getByRole("combobox", { name: "Kategorien" });
}

test("the trigger is a labelled combobox that opens a search list", async () => {
  const user = userEvent.setup();
  render(<Harness />);
  assert.equal(trigger().getAttribute("aria-expanded"), "false");
  await user.click(trigger());
  assert.equal(trigger().getAttribute("aria-expanded"), "true");
  assert.ok(screen.getByRole("combobox", { name: "Suchen" }));
  assert.equal(screen.getAllByRole("option").length, 3);
});

test("typing filters by label and Enter toggles the highlighted option", async () => {
  const user = userEvent.setup();
  render(<Harness />);
  await user.click(trigger());
  await user.keyboard("smo");
  const options = screen.getAllByRole("option");
  assert.deepEqual(options.map((option) => option.textContent), ["Smoker"]);
  await user.keyboard("{Enter}");
  assert.equal(screen.getByTestId("value").textContent, "3");
  assert.equal(screen.getByRole("option", { name: "Smoker" }).dataset.checked, "true");
});

test("an unmatched search shows the empty text", async () => {
  const user = userEvent.setup();
  render(<Harness />);
  await user.click(trigger());
  await user.keyboard("xyz");
  assert.ok(screen.getByText("Nichts gefunden"));
});

test("selected values show as removable chips, unknown ones by value", async () => {
  const user = userEvent.setup();
  render(<Harness initial={["1", "99"]} />);
  const list = screen.getByRole("list", { name: "Ausgewählt" });
  assert.equal(list.textContent, "Ersatzteile99");
  await user.click(screen.getByRole("button", { name: "99 entfernen" }));
  assert.equal(screen.getByTestId("value").textContent, "1");
});

test("Escape closes the list and returns focus to the trigger", async () => {
  const user = userEvent.setup();
  render(<Harness />);
  await user.click(trigger());
  await user.keyboard("{Escape}");
  assert.equal(trigger().getAttribute("aria-expanded"), "false");
  assert.equal(document.activeElement, trigger());
});

test("disabled blocks opening and removing", async () => {
  const user = userEvent.setup();
  render(<Harness initial={["1"]} disabled />);
  assert.equal(trigger().hasAttribute("disabled"), true);
  await user.click(trigger());
  assert.equal(trigger().getAttribute("aria-expanded"), "false");
  const remove = screen.getByRole("button", { name: "Ersatzteile entfernen" });
  assert.equal(remove.hasAttribute("disabled"), true);
});

test("the trigger carries the outline states and no forbidden hover", () => {
  render(<Harness />);
  const classes = trigger().className;
  for (const name of ["hover:", "focus-visible:", "disabled:"]) {
    assert.ok(classes.includes(name), name);
  }
  assert.ok(!/hover:bg-(muted|accent)/.test(classes));
});
