import assert from "node:assert/strict";
import { test } from "node:test";
import * as ui from "@web-app-kit/frontend/ui";
import * as command from "@web-app-kit/frontend/command";
import { CATALOG_ENTRIES } from "./entries";
import { HELPER_EXPORTS } from "./export-exceptions";

const uiExports = [...Object.keys(ui), ...Object.keys(command)].sort();
const entryExports = CATALOG_ENTRIES.flatMap((entry) => entry.exports);

test("every ui export has a catalog entry or is a listed helper", () => {
  const covered = new Set([...entryExports, ...Object.keys(HELPER_EXPORTS)]);
  const missing = uiExports.filter((name) => !covered.has(name));
  assert.deepEqual(missing, [], `Katalog-Eintrag fehlt für: ${missing.join(", ")}`);
});

test("catalog entries only name existing ui exports", () => {
  const known = new Set(uiExports);
  const unknown = entryExports.filter((name) => !known.has(name));
  assert.deepEqual(unknown, [], `Unbekannte Exporte im Katalog: ${unknown.join(", ")}`);
});

test("helper exceptions only name existing ui exports", () => {
  const known = new Set(uiExports);
  const unknown = Object.keys(HELPER_EXPORTS).filter((name) => !known.has(name));
  assert.deepEqual(unknown, []);
});

test("each export belongs to exactly one entry and is no helper", () => {
  const duplicates = entryExports.filter((name, index) => entryExports.indexOf(name) !== index);
  const helpers = entryExports.filter((name) => name in HELPER_EXPORTS);
  assert.deepEqual([...duplicates, ...helpers], []);
});

test("entries have unique slugs, variants and states", () => {
  const slugs = CATALOG_ENTRIES.map((entry) => entry.slug);
  assert.equal(new Set(slugs).size, slugs.length);
  for (const entry of CATALOG_ENTRIES) {
    assert.ok(entry.description.length > 0, `${entry.name}: Beschreibung fehlt`);
    assert.ok(entry.variants.length > 0, `${entry.name}: Variante fehlt`);
    assert.ok(entry.states.length > 0, `${entry.name}: Zustand fehlt`);
    const keys = entry.variants.map((variant) => variant.key);
    assert.equal(new Set(keys).size, keys.length, `${entry.name}: doppelte Varianten`);
  }
});
