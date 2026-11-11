import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen, waitFor } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { Button, Popover, PopoverContent, PopoverTitle, PopoverTrigger } = await import(
  "./index.js"
);

test.afterEach(cleanup);

function renderPopover() {
  render(
    <Popover>
      <PopoverTrigger asChild>
        <Button variant="outline">Optionen</Button>
      </PopoverTrigger>
      <PopoverContent>
        <PopoverTitle>Raster</PopoverTitle>
        <Button>Anwenden</Button>
      </PopoverContent>
    </Popover>,
  );
  return screen.getByRole("button", { name: "Optionen" });
}

test("click opens the popover and moves focus into it", async () => {
  const user = userEvent.setup();
  const trigger = renderPopover();
  assert.equal(trigger.getAttribute("aria-expanded"), "false");
  await user.click(trigger);
  const content = await screen.findByRole("dialog");
  assert.equal(trigger.getAttribute("aria-expanded"), "true");
  assert.equal(content.dataset.slot, "popover-content");
  assert.ok(content.className.includes("bg-popover"));
  await waitFor(() => assert.ok(content.contains(document.activeElement)));
});

test("second click on the trigger closes the popover", async () => {
  const user = userEvent.setup();
  const trigger = renderPopover();
  await user.click(trigger);
  await screen.findByRole("dialog");
  await user.click(trigger);
  await waitFor(() => assert.equal(screen.queryByRole("dialog"), null));
  assert.equal(trigger.getAttribute("aria-expanded"), "false");
});

test("Escape closes the popover and returns focus to the trigger", async () => {
  const user = userEvent.setup();
  const trigger = renderPopover();
  await user.click(trigger);
  await screen.findByRole("dialog");
  await user.keyboard("{Escape}");
  await waitFor(() => assert.equal(screen.queryByRole("dialog"), null));
  await waitFor(() => assert.equal(document.activeElement, trigger));
});
