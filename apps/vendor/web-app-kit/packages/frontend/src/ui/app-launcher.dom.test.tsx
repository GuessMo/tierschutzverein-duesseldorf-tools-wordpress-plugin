import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen, waitFor } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { AppLauncher } = await import("./index.js");

test.afterEach(cleanup);

const ITEMS = [
  { id: "website", label: "Website", href: "/wp-admin/", icon: <svg /> },
  { id: "quellen", label: "Quellen", href: "/quellen/", icon: <svg /> },
];

function renderLauncher() {
  render(<AppLauncher items={ITEMS} label="Tools" newTabHint="(öffnet in neuem Tab)" />);
  return screen.getByRole("button", { name: "Tools" });
}

test("trigger is an icon button labelled by the label prop", () => {
  const trigger = renderLauncher();
  assert.equal(trigger.getAttribute("aria-expanded"), "false");
});

test("click opens the list with one new-tab link per item", async () => {
  const user = userEvent.setup();
  await user.click(renderLauncher());
  const list = await screen.findByRole("list", { name: "Tools" });
  const links = list.querySelectorAll("a");
  assert.equal(links.length, 2);
  assert.equal(links[1]?.getAttribute("href"), "/quellen/");
  assert.equal(links[1]?.getAttribute("target"), "_blank");
  assert.equal(links[1]?.getAttribute("rel"), "noopener");
});

test("links carry the new-tab hint and interactive surface states", async () => {
  const user = userEvent.setup();
  await user.click(renderLauncher());
  const link = await screen.findByRole("link", { name: "Website (öffnet in neuem Tab)" });
  assert.ok(link.className.includes("focus-visible:ring-2"));
  assert.ok(link.className.includes("not-disabled:hover:bg-primary-subtle"));
  assert.ok(link.className.includes("not-disabled:active:translate-y-px"));
});

test("Tab moves focus into the list, Escape closes and returns focus", async () => {
  const user = userEvent.setup();
  const trigger = renderLauncher();
  await user.click(trigger);
  await screen.findByRole("list", { name: "Tools" });
  await user.keyboard("{Escape}");
  await waitFor(() => assert.equal(screen.queryByRole("list", { name: "Tools" }), null));
  await waitFor(() => assert.equal(document.activeElement, trigger));
});
