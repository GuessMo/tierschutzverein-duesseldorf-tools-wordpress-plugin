import assert from "node:assert/strict";
import { readFileSync } from "node:fs";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const PACKAGE_JSON_URL = new URL("../../package.json", import.meta.url);
const packageVersion: string = JSON.parse(readFileSync(PACKAGE_JSON_URL, "utf8")).version;

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

test("kitLabel adds the kit version from the frontend package", () => {
  render(<AppVersion label="Version" version="v1.2.0" kitLabel="Kit" />);
  const version = screen.getByText("v1.2.0").parentElement;
  assert.match(packageVersion, /^\d+\.\d+\.\d+/);
  assert.equal(version?.textContent, `Versionv1.2.0·Kit${packageVersion}`);
  assert.equal(screen.getByText("·").getAttribute("aria-hidden"), "true");
});

test("no kit version without kitLabel", () => {
  render(<AppVersion label="Version" version="v1.2.0" />);
  assert.equal(screen.getByText("v1.2.0").parentElement?.textContent, "Versionv1.2.0");
});
