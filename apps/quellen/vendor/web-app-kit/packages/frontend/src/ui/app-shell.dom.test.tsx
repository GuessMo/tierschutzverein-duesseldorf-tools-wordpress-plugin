import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, fireEvent, render, screen } = await import("@testing-library/react");
const { AppShell, APP_SHELL_CONTENT_ID } = await import("./index.js");

test.afterEach(cleanup);

function renderShellWithPanels() {
  render(
    <AppShell
      brand="Taste"
      homeHref="/"
      skipLinkLabel="Zum Inhalt"
      actions={<button type="button">Neu</button>}
      controls={<button type="button">Theme</button>}
      navigation={<p>Projekte</p>}
      navigationLabel="Hauptnavigation"
      detail={<p>Details</p>}
      detailLabel="Details"
    >
      <p>Inhalt</p>
    </AppShell>,
  );
}

function renderPlainShell(homeHref?: string) {
  render(
    <AppShell brand="Taste" homeHref={homeHref} skipLinkLabel="Zum Inhalt">
      <p>Inhalt</p>
    </AppShell>,
  );
}

test("renders header, main and the optional navigation and detail landmarks", () => {
  renderShellWithPanels();
  assert.ok(screen.getByRole("banner"));
  assert.ok(screen.getByRole("main").textContent?.includes("Inhalt"));
  assert.ok(screen.getByRole("navigation", { name: "Hauptnavigation" }));
  assert.ok(screen.getByRole("complementary", { name: "Details" }));
});

test("skip link targets the focusable main region and has hover and pressed states", () => {
  renderPlainShell();
  const skip = screen.getByRole("link", { name: "Zum Inhalt" });
  const main = screen.getByRole("main");
  assert.equal(skip.getAttribute("href"), `#${APP_SHELL_CONTENT_ID}`);
  assert.equal(main.id, APP_SHELL_CONTENT_ID);
  assert.equal(main.tabIndex, -1);
  for (const token of ["hover:overlay-hover", "active:translate-y-px", "focus:ring-3"]) {
    assert.ok(skip.classList.contains(token), token);
  }
});

test("navigation and detail landmarks are omitted when not provided", () => {
  renderPlainShell();
  assert.equal(screen.queryByRole("navigation"), null);
  assert.equal(screen.queryByRole("complementary"), null);
});

test("brand renders as an interactive home link only with homeHref", () => {
  renderPlainShell("/");
  const brand = screen.getByRole("link", { name: "Taste" });
  assert.equal(brand.getAttribute("href"), "/");
  for (const token of ["not-disabled:hover:bg-primary-subtle", "focus-visible:ring-3", "-ml-2"]) {
    assert.ok(brand.classList.contains(token), token);
  }
  cleanup();
  renderPlainShell();
  assert.equal(screen.queryByRole("link", { name: "Taste" }), null);
  const textBrand = document.querySelector("[data-slot=app-shell-brand]");
  assert.equal(textBrand?.tagName, "SPAN");
  assert.ok(textBrand?.classList.contains("-ml-2"));
});

test("navigation and detail require their landmark labels", () => {
  const navigationWithoutLabel = (
    // @ts-expect-error a navigation panel needs navigationLabel for its landmark name
    <AppShell brand="T" skipLinkLabel="S" navigation={<p>Nav</p>}>
      <p>Inhalt</p>
    </AppShell>
  );
  const detailWithoutLabel = (
    // @ts-expect-error a detail panel needs detailLabel for its landmark name
    <AppShell brand="T" skipLinkLabel="S" detail={<p>Detail</p>}>
      <p>Inhalt</p>
    </AppShell>
  );
  assert.ok(navigationWithoutLabel && detailWithoutLabel);
});

test("renderLink renders the brand link with the shared href, classes and data-slot", () => {
  const received: { href: string; className: string; slot: string }[] = [];
  render(
    <AppShell
      brand="Taste"
      homeHref="/start"
      skipLinkLabel="Zum Inhalt"
      renderLink={({ href, className, children, ...rest }) => {
        received.push({ href, className, slot: rest["data-slot"] });
        return (
          <a href={href} className={className} data-router-link="true" {...rest}>
            {children}
          </a>
        );
      }}
    >
      <p>Inhalt</p>
    </AppShell>,
  );
  const brand = screen.getByRole("link", { name: "Taste" });
  assert.equal(received.length > 0, true);
  assert.equal(received[0].href, "/start");
  assert.equal(received[0].slot, "app-shell-brand");
  assert.equal(brand.dataset.routerLink, "true");
  assert.equal(brand.dataset.slot, "app-shell-brand");
  const tokens = ["not-disabled:hover:bg-primary-subtle", "not-disabled:active:translate-y-px"];
  for (const token of tokens) {
    assert.ok(received[0].className.split(" ").includes(token), token);
    assert.ok(brand.classList.contains(token), token);
  }
  assert.ok(brand.classList.contains("focus-visible:ring-3"));
});

test("panels start collapsed on small screens and toggle via a labelled button", () => {
  renderShellWithPanels();
  const toggle = screen.getByRole("button", { name: "Details" });
  const content = document.getElementById(toggle.getAttribute("aria-controls") ?? "");
  assert.equal(toggle.getAttribute("aria-expanded"), "false");
  assert.ok(content?.textContent?.includes("Details"));
  assert.ok(content?.classList.contains("max-md:hidden"));
  fireEvent.click(toggle);
  assert.equal(toggle.getAttribute("aria-expanded"), "true");
  assert.equal(content?.classList.contains("max-md:hidden"), false);
  fireEvent.click(toggle);
  assert.equal(toggle.getAttribute("aria-expanded"), "false");
});

test("panel toggles are hidden from md, interactive, and cap the open panel height", () => {
  renderShellWithPanels();
  for (const name of ["Hauptnavigation", "Details"]) {
    const toggle = screen.getByRole("button", { name });
    const tokens = [
      "md:hidden",
      "min-h-11",
      "not-disabled:hover:bg-primary-subtle",
      "focus-visible:ring-inset",
    ];
    for (const token of tokens) {
      assert.ok(toggle.classList.contains(token), `${name}: ${token}`);
    }
    assert.ok(toggle.classList.contains("focus-visible:ring-3"));
    const panel = toggle.closest("[data-slot=app-shell-panel]");
    assert.ok(panel?.classList.contains("max-md:max-h-[60dvh]"));
  }
});
