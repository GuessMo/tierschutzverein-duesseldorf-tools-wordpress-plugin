import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const { AppShell, AppVersion } = await import("./index.js");

test.afterEach(cleanup);

test("footer slot renders a contentinfo landmark with the app version", () => {
  render(
    <AppShell
      brand="Taste"
      skipLinkLabel="Zum Inhalt"
      footer={<AppVersion label="Version" version="v0.7.1-3-gabc1234" />}
    >
      <p>Inhalt</p>
    </AppShell>,
  );
  const footer = screen.getByRole("contentinfo");
  assert.equal(footer.textContent, "Versionv0.7.1-3-gabc1234");
});

test("no footer landmark without the footer slot", () => {
  render(
    <AppShell brand="Taste" skipLinkLabel="Zum Inhalt">
      <p>Inhalt</p>
    </AppShell>,
  );
  assert.equal(screen.queryByRole("contentinfo"), null);
});
