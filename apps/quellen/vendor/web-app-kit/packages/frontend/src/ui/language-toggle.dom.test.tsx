import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, fireEvent, render, screen } = await import("@testing-library/react");
const { LanguageToggle } = await import("./index.js");

test.afterEach(cleanup);

const LANGUAGES = [
  { code: "de", label: "Deutsch" },
  { code: "en", label: "English" },
];

function renderToggle(
  value: string,
  onChange: (code: string) => void,
  describe = ({ code, label }: { code: string; label: string }) =>
    `Sprache: ${label} (${code.toUpperCase()})`,
) {
  render(
    <LanguageToggle languages={LANGUAGES} value={value} onChange={onChange} describe={describe} />,
  );
  return screen.getByRole("button");
}

test("accessible name comes from describe and contains the visible code", () => {
  const button = renderToggle("en", () => undefined);
  assert.equal(button.getAttribute("aria-label"), "Sprache: English (EN)");
  assert.equal(button.textContent, "EN");
  assert.ok(button.getAttribute("aria-label")?.includes(button.textContent ?? "-"));
  assert.equal(button.dataset.variant, "ghost");
  assert.ok(button.querySelector(".tabler-icon-language"));
});

test("visible code is prepended when the description omits it", () => {
  const button = renderToggle("de", () => undefined, ({ label }) => `Sprache: ${label}`);
  assert.equal(button.getAttribute("aria-label"), "DE Sprache: Deutsch");
});

test("click cycles to the next language and wraps around", () => {
  const changes: string[] = [];
  renderToggle("de", (code) => changes.push(code));
  fireEvent.click(screen.getByRole("button"));
  cleanup();
  renderToggle("en", (code) => changes.push(code));
  fireEvent.click(screen.getByRole("button"));
  assert.deepEqual(changes, ["en", "de"]);
});

test("unknown value falls back to the first language", () => {
  const button = renderToggle("fr", () => undefined);
  assert.equal(button.getAttribute("aria-label"), "Sprache: Deutsch (DE)");
});
