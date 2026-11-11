import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const { Tabs, TabsContent, TabsList, TabsTrigger } = await import("./index.js");

test.afterEach(cleanup);

function renderTabs(orientation: "horizontal" | "vertical" = "horizontal") {
  render(
    <Tabs defaultValue="general" orientation={orientation}>
      <TabsList aria-label="Einstellungen">
        <TabsTrigger value="general">Allgemein</TabsTrigger>
        <TabsTrigger value="members">Mitglieder</TabsTrigger>
        <TabsTrigger value="billing" disabled>
          Abrechnung
        </TabsTrigger>
      </TabsList>
      <TabsContent value="general">Inhalt Allgemein</TabsContent>
      <TabsContent value="members">Inhalt Mitglieder</TabsContent>
    </Tabs>,
  );
}

test("tablist, tabs and panel carry roles, names and selection", () => {
  renderTabs();
  assert.ok(screen.getByRole("tablist", { name: "Einstellungen" }));
  const general = screen.getByRole("tab", { name: "Allgemein" });
  assert.equal(general.getAttribute("aria-selected"), "true");
  assert.equal(general.dataset.state, "active");
  assert.equal(screen.getByRole("tabpanel").textContent, "Inhalt Allgemein");
});

test("arrow keys move focus and activate the next enabled tab", async () => {
  const user = userEvent.setup();
  renderTabs();
  await user.tab();
  assert.equal(document.activeElement, screen.getByRole("tab", { name: "Allgemein" }));
  await user.keyboard("{ArrowRight}");
  const members = screen.getByRole("tab", { name: "Mitglieder" });
  assert.equal(document.activeElement, members);
  assert.equal(members.getAttribute("aria-selected"), "true");
  await user.keyboard("{ArrowRight}");
  assert.equal(document.activeElement, screen.getByRole("tab", { name: "Allgemein" }));
});

test("vertical tabs react to up and down arrows", async () => {
  const user = userEvent.setup();
  renderTabs("vertical");
  assert.equal(screen.getByRole("tablist").getAttribute("aria-orientation"), "vertical");
  await user.tab();
  await user.keyboard("{ArrowDown}");
  assert.equal(document.activeElement, screen.getByRole("tab", { name: "Mitglieder" }));
});

test("disabled tab cannot be selected", async () => {
  const user = userEvent.setup();
  renderTabs();
  const billing = screen.getByRole("tab", { name: "Abrechnung" });
  assert.equal(billing.hasAttribute("disabled"), true);
  await user.click(billing);
  assert.equal(billing.getAttribute("aria-selected"), "false");
});

const STATE_CLASSES = [
  "data-[state=inactive]:not-disabled:hover:bg-primary-subtle",
  "data-[state=inactive]:not-disabled:active:inset-ring-primary",
  "group-data-[variant=default]/tabs-list:data-active:border-primary",
  "group-data-[variant=default]/tabs-list:data-active:inset-ring-primary",
  "dark:group-data-[variant=default]/tabs-list:data-active:border-primary",
  "dark:group-data-[variant=default]/tabs-list:data-active:bg-primary-subtle",
  "group-data-[variant=line]/tabs-list:data-active:after:opacity-100",
  "focus-visible:outline-ring",
  "focus-visible:ring-ring/50",
  "disabled:opacity-50",
];

test("tab trigger carries hover, pressed, selected, focus and disabled classes", () => {
  renderTabs();
  const classes = screen.getByRole("tab", { name: "Allgemein" }).className.split(" ");
  for (const token of STATE_CLASSES) assert.ok(classes.includes(token), token);
  assert.equal(classes.some((name) => /hover:bg-(muted|accent)/.test(name)), false);
});
