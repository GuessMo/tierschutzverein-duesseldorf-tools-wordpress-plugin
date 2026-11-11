import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { userEvent } = await import("@testing-library/user-event");
const ui = await import("./index.js");
const { Button, Card, CardAction, CardDescription, CardHeader, CardTitle } = ui;

test.afterEach(cleanup);

function renderButtonCard(onClick: () => void) {
  render(
    <Card interactive asChild>
      <button type="button" onClick={onClick} aria-labelledby="t1" aria-describedby="d1">
        <CardHeader asChild>
          <span>
            <CardTitle asChild>
              <span id="t1">Open project</span>
            </CardTitle>
            <CardDescription asChild>
              <span id="d1">Long description that should not become the name</span>
            </CardDescription>
          </span>
        </CardHeader>
      </button>
    </Card>,
  );
  return screen.getByRole("button", { name: "Open project" });
}

test("keyboard focus matches :focus-visible and carries the focus ring classes", async () => {
  const user = userEvent.setup();
  const card = renderButtonCard(() => undefined);
  await user.tab();
  assert.equal(card.matches(":focus-visible"), true);
  for (const token of ["focus-visible:ring-3", "focus-visible:outline-hidden", "cursor-pointer"]) {
    assert.ok(card.classList.contains(token), token);
  }
  assert.equal(card.dataset.interactive, "true");
});

test("button card contains only phrasing content and keeps a short accessible name", () => {
  const card = renderButtonCard(() => undefined);
  assert.equal(card.querySelector("div"), null);
  assert.equal(card.getAttribute("aria-describedby"), "d1");
  assert.equal(card.querySelector("[data-slot=card-title]")?.tagName, "SPAN");
});

test("interactive button card is reachable by Tab and activates with Enter and Space", async () => {
  const user = userEvent.setup();
  let clicks = 0;
  const card = renderButtonCard(() => (clicks += 1));
  await user.tab();
  assert.equal(document.activeElement, card);
  await user.keyboard("{Enter}");
  await user.keyboard(" ");
  assert.equal(clicks, 2);
});

test("interactive link card is reachable by Tab and activates with Enter", async () => {
  const user = userEvent.setup();
  let visited = 0;
  const visit = (event: { preventDefault: () => void }) => {
    event.preventDefault();
    visited += 1;
  };
  render(
    <Card interactive asChild>
      <a href="/projects/1" onClick={visit}>
        <CardTitle>Project 1</CardTitle>
      </a>
    </Card>,
  );
  await user.tab();
  assert.equal(document.activeElement, screen.getByRole("link", { name: "Project 1" }));
  await user.keyboard("{Enter}");
  assert.equal(visited, 1);
});

test("interactive card uses the primary hover, selected, pressed and aria-disabled states", () => {
  const card = renderButtonCard(() => undefined);
  const tokens = [
    "not-disabled:hover:bg-primary-subtle",
    "not-disabled:active:translate-y-px",
    "aria-disabled:pointer-events-none",
    "aria-selected:bg-primary-subtle",
    "aria-[current=page]:inset-ring-primary",
    "data-selected:inset-ring-2",
  ];
  for (const token of tokens) {
    assert.ok(ui.INTERACTIVE_SURFACE_CLASSES.split(" ").includes(token), token);
    assert.ok(card.classList.contains(token), token);
  }
});

test("stretched link card keeps the action button separately reachable", async () => {
  const user = userEvent.setup();
  render(
    <Card className={ui.STRETCHED_CARD_CLASSES}>
      <CardHeader>
        <CardTitle>
          <a href="/projects/2" className={ui.STRETCHED_LINK_CLASSES}>
            Project 2
          </a>
        </CardTitle>
        <CardAction className="relative z-10">
          <Button>Archive</Button>
        </CardAction>
      </CardHeader>
    </Card>,
  );
  await user.tab();
  assert.equal(document.activeElement, screen.getByRole("link", { name: "Project 2" }));
  await user.tab();
  assert.equal(document.activeElement, screen.getByRole("button", { name: "Archive" }));
  assert.equal(screen.getByRole("link").closest("button"), null);
  const stretched = ui.STRETCHED_CARD_CLASSES.split(" ");
  assert.ok(stretched.includes("aria-selected:inset-ring-primary"));
  assert.ok(stretched.some((token) => token.endsWith(":bg-primary-subtle")));
});

test("static card stays a non-focusable div without interactive styling", () => {
  render(<Card>Static</Card>);
  const card = screen.getByText("Static");
  assert.equal(card.tagName, "DIV");
  assert.equal(card.tabIndex, -1);
  assert.equal(card.classList.contains("cursor-pointer"), false);
  assert.equal(card.dataset.interactive, undefined);
});

test("interactive without asChild is rejected by the type", () => {
  // @ts-expect-error interactive cards need asChild so a button or link supplies the semantics
  const element = <Card interactive>Missing semantics</Card>;
  assert.ok(element);
});
