import assert from "node:assert/strict";
import { test } from "node:test";
import { createRef, useState } from "react";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, fireEvent, render, screen } = await import("@testing-library/react");
const { AppShell, SidePanel } = await import("./index.js");

test.afterEach(cleanup);

test("left panel draws its border on the inner edge with sidebar tokens", () => {
  const { container } = render(<SidePanel side="left">Nav</SidePanel>);
  const panel = container.querySelector("[data-slot=side-panel]");
  assert.ok(panel);
  for (const token of ["md:border-r", "bg-sidebar", "border-sidebar-border", "md:w-64"]) {
    assert.ok(panel.classList.contains(token), token);
  }
  assert.equal(panel.classList.contains("md:border-l"), false);
});

test("right panel with width lg draws its border on the left edge", () => {
  const { container } = render(
    <SidePanel side="right" width="lg">
      Detail
    </SidePanel>,
  );
  const panel = container.querySelector("[data-slot=side-panel]");
  assert.ok(panel?.classList.contains("md:border-l"));
  assert.ok(panel?.classList.contains("md:w-96"));
});

test("closed panel renders nothing", () => {
  const { container } = render(
    <SidePanel side="left" open={false}>
      Nav
    </SidePanel>,
  );
  assert.equal(container.innerHTML, "");
});

test("titled panel is a group named by its heading", () => {
  render(
    <SidePanel side="right" title="Eigenschaften">
      Detail
    </SidePanel>,
  );
  const heading = screen.getByRole("heading", { name: "Eigenschaften" });
  const group = screen.getByRole("group", { name: "Eigenschaften" });
  assert.equal(group.getAttribute("aria-labelledby"), heading.id);
});

test("panel with only a close button renders no empty heading and no group", () => {
  render(
    <SidePanel side="right" onOpenChange={() => undefined} closeLabel="Schliessen">
      Detail
    </SidePanel>,
  );
  assert.equal(screen.queryByRole("heading"), null);
  assert.equal(screen.queryByRole("group"), null);
});

test("panel without title, actions or close handler has no header", () => {
  const { container } = render(<SidePanel side="left">Nav</SidePanel>);
  assert.equal(container.querySelector("[data-slot=side-panel-header]"), null);
});

function ClosableDetail({ returnFocus }: { returnFocus: boolean }) {
  const [isOpen, setIsOpen] = useState(true);
  const triggerRef = createRef<HTMLButtonElement>();
  return (
    <AppShell
      brand="T"
      skipLinkLabel="Skip"
      actions={<button ref={triggerRef} type="button">Trigger</button>}
      detail={
        <SidePanel
          side="right"
          open={isOpen}
          onOpenChange={setIsOpen}
          closeLabel="Schliessen"
          returnFocusRef={returnFocus ? triggerRef : undefined}
        >
          Detail
        </SidePanel>
      }
      detailLabel="Details"
    >
      <p>Inhalt</p>
    </AppShell>
  );
}

test("closing moves focus to the main content by default", () => {
  render(<ClosableDetail returnFocus={false} />);
  const close = screen.getByRole("button", { name: "Schliessen" });
  close.focus();
  fireEvent.click(close);
  assert.equal(screen.queryByRole("button", { name: "Schliessen" }), null);
  assert.equal(document.activeElement, screen.getByRole("main"));
});

test("closing moves focus to returnFocusRef when given", () => {
  render(<ClosableDetail returnFocus />);
  fireEvent.click(screen.getByRole("button", { name: "Schliessen" }));
  assert.equal(document.activeElement, screen.getByRole("button", { name: "Trigger" }));
});
