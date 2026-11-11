import assert from "node:assert/strict";
import { test } from "node:test";
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
const ui = await import("./command-entry.js");

test.afterEach(cleanup);

function renderCommand(onSelect: (value: string) => void = () => {}) {
  render(
    <ui.Command label="Befehle">
      <ui.CommandInput placeholder="Suchen" />
      <ui.CommandList>
        <ui.CommandEmpty>Nichts gefunden</ui.CommandEmpty>
        <ui.CommandGroup heading="Ebene">
          <ui.CommandItem onSelect={onSelect}>Kopieren</ui.CommandItem>
          <ui.CommandItem onSelect={onSelect} disabled>
            Löschen
          </ui.CommandItem>
          <ui.CommandItem onSelect={onSelect}>
            Einfügen
            <ui.CommandShortcut>⌘V</ui.CommandShortcut>
          </ui.CommandItem>
        </ui.CommandGroup>
        <ui.CommandSeparator />
      </ui.CommandList>
    </ui.Command>,
  );
  return screen.getByRole("combobox", { name: "Befehle" });
}

test("arrow keys move the highlight and Enter selects", async () => {
  const user = userEvent.setup();
  const selected: string[] = [];
  const input = renderCommand((value) => selected.push(value));
  await user.click(input);
  await user.keyboard("{ArrowDown}{Enter}");
  assert.deepEqual(selected, ["Einfügen⌘V"]);
});

test("disabled items are skipped and cannot be chosen", async () => {
  const user = userEvent.setup();
  const selected: string[] = [];
  renderCommand((value) => selected.push(value));
  const disabled = screen.getByRole("option", { name: "Löschen" });
  assert.equal(disabled.getAttribute("aria-disabled"), "true");
  await user.click(disabled);
  assert.deepEqual(selected, []);
});

test("typing filters and shows the empty text when nothing matches", async () => {
  const user = userEvent.setup();
  const input = renderCommand();
  await user.click(input);
  await user.keyboard("kop");
  assert.deepEqual(
    screen.getAllByRole("option").map((option) => option.textContent),
    ["Kopieren"],
  );
  await user.keyboard("xyz");
  assert.ok(screen.getByText("Nichts gefunden"));
});

test("the highlighted item uses the kit highlight, not bg-muted alone", () => {
  renderCommand();
  const item = screen.getByRole("option", { name: "Kopieren" });
  assert.equal(item.getAttribute("aria-selected"), "true");
  for (const name of [
    "data-selected:bg-primary-subtle",
    "data-selected:inset-ring-2",
    "data-selected:inset-ring-ring",
  ]) {
    assert.ok(item.className.includes(name), name);
  }
  assert.ok(!item.className.includes("data-selected:bg-muted"));
});

test("CommandDialog names the dialog from its props", async () => {
  render(
    <ui.CommandDialog open title="Befehlspalette" description="Befehl suchen">
      <ui.Command>
        <ui.CommandInput placeholder="Suchen" />
      </ui.Command>
    </ui.CommandDialog>,
  );
  const dialog = await screen.findByRole("dialog", { name: "Befehlspalette" });
  assert.ok(dialog);
  assert.equal(screen.queryByRole("button", { name: /schließen/i }), null);
});
