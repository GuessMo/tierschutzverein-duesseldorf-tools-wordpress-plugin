import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, fireEvent, render, screen, waitFor } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const ui = await import("./index.js");

test.afterEach(cleanup);

function renderContextMenu() {
  render(
    <ui.ContextMenu>
      <ui.ContextMenuTrigger tabIndex={0}>Fläche</ui.ContextMenuTrigger>
      <ui.ContextMenuContent>
        <ui.ContextMenuLabel>Ebene</ui.ContextMenuLabel>
        <ui.ContextMenuItem>
          Kopieren
          <ui.ContextMenuShortcut>⌘C</ui.ContextMenuShortcut>
        </ui.ContextMenuItem>
        <ui.ContextMenuSeparator />
        <ui.ContextMenuCheckboxItem checked>Raster</ui.ContextMenuCheckboxItem>
        <ui.ContextMenuRadioGroup value="a">
          <ui.ContextMenuRadioItem value="a">Links</ui.ContextMenuRadioItem>
        </ui.ContextMenuRadioGroup>
        <ui.ContextMenuSub>
          <ui.ContextMenuSubTrigger>Anordnen</ui.ContextMenuSubTrigger>
          <ui.ContextMenuSubContent>
            <ui.ContextMenuItem>Nach vorn</ui.ContextMenuItem>
          </ui.ContextMenuSubContent>
        </ui.ContextMenuSub>
      </ui.ContextMenuContent>
    </ui.ContextMenu>,
  );
  return screen.getByText("Fläche");
}

test("right click opens the menu with all item roles", async () => {
  const user = userEvent.setup();
  const trigger = renderContextMenu();
  await user.pointer({ keys: "[MouseRight]", target: trigger });
  const menu = await screen.findByRole("menu");
  assert.equal(menu.dataset.slot, "context-menu-content");
  assert.ok(screen.getByRole("menuitem", { name: /Kopieren/ }));
  assert.equal(screen.getByRole("menuitemcheckbox", { name: "Raster" }).dataset.state, "checked");
  assert.equal(screen.getByRole("menuitemradio", { name: "Links" }).dataset.state, "checked");
  assert.equal(screen.getByRole("separator").dataset.slot, "context-menu-separator");
  assert.ok(screen.getByRole("menuitem", { name: "Anordnen" }));
});

for (const [label, keys] of [
  ["Shift+F10", "{Shift>}{F10}{/Shift}"],
  ["ContextMenu key", "{ContextMenu}"],
] as const) {
  test(`${label} on the focused trigger opens the menu and focuses the first item`, async () => {
    const user = userEvent.setup();
    const trigger = renderContextMenu();
    trigger.focus();
    await user.keyboard(keys);
    await screen.findByRole("menu");
    const first = screen.getByRole("menuitem", { name: /Kopieren/ });
    await waitFor(() => assert.equal(document.activeElement, first));
    assert.equal(first.hasAttribute("data-highlighted"), true);
  });
}

test("Escape closes the menu and returns focus to the trigger", async () => {
  const user = userEvent.setup();
  const trigger = renderContextMenu();
  trigger.focus();
  await user.keyboard("{Shift>}{F10}{/Shift}");
  await screen.findByRole("menu");
  await user.keyboard("{Escape}");
  await waitFor(() => assert.equal(screen.queryByRole("menu"), null));
  await waitFor(() => assert.equal(document.activeElement, trigger));
});

test("other keys and a prevented onKeyDown do not open the menu", async () => {
  render(
    <ui.ContextMenu>
      <ui.ContextMenuTrigger tabIndex={0} onKeyDown={(event) => event.preventDefault()}>
        Gesperrt
      </ui.ContextMenuTrigger>
      <ui.ContextMenuContent>
        <ui.ContextMenuItem>Kopieren</ui.ContextMenuItem>
      </ui.ContextMenuContent>
    </ui.ContextMenu>,
  );
  const trigger = screen.getByText("Gesperrt");
  fireEvent.keyDown(trigger, { key: "F10", shiftKey: true });
  fireEvent.keyDown(trigger, { key: "Enter" });
  assert.equal(screen.queryByRole("menu"), null);
});

test("highlighted items carry the subtle surface and the opaque inner ring", async () => {
  const user = userEvent.setup();
  const trigger = renderContextMenu();
  await user.pointer({ keys: "[MouseRight]", target: trigger });
  await screen.findByRole("menu");
  const expected = [
    "focus:bg-primary-subtle",
    "data-highlighted:inset-ring-2",
    "data-highlighted:inset-ring-ring",
  ];
  for (const role of ["menuitem", "menuitemcheckbox", "menuitemradio"] as const) {
    const classes = screen.getAllByRole(role)[0].className.split(" ");
    for (const token of expected) assert.ok(classes.includes(token), `${role}: ${token}`);
  }
  const indicator = document.querySelector("[data-slot=context-menu-checkbox-item-indicator]");
  assert.ok(indicator?.className.includes("text-primary"));
});
