import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { AccountMenu } = await import("./index.js");

test.afterEach(cleanup);

function renderMenu(onLogout: () => void) {
  render(
    <AccountMenu
      name="Erika Muster"
      initials="EM"
      label="Konto: Erika Muster"
      logoutLabel="Abmelden"
      onLogout={onLogout}
    />,
  );
  return screen.getByRole("button", { name: "Konto: Erika Muster" });
}

test("trigger shows the initials as a filled button with outline focus", () => {
  const trigger = renderMenu(() => undefined);
  assert.equal(trigger.textContent, "EM");
  assert.ok(trigger.className.includes("bg-primary"));
  assert.ok(trigger.className.includes("focus-visible:outline"));
});

test("menu shows the name and calls onLogout on select", async () => {
  const user = userEvent.setup();
  let calls = 0;
  await user.click(renderMenu(() => (calls += 1)));
  await screen.findByText("Erika Muster");
  await user.click(screen.getByRole("menuitem", { name: "Abmelden" }));
  assert.equal(calls, 1);
});

test("keyboard: Enter opens, arrow selects, Enter logs out", async () => {
  const user = userEvent.setup();
  let calls = 0;
  const trigger = renderMenu(() => (calls += 1));
  trigger.focus();
  await user.keyboard("{Enter}");
  await screen.findByRole("menu");
  await user.keyboard("{ArrowDown}{Enter}");
  assert.equal(calls, 1);
});
