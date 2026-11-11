import assert from "node:assert/strict";
import { test } from "node:test";
import { contrastLabel } from "./contrast-label";

test("contrast label states verdict against the minimum", () => {
  assert.equal(contrastLabel(5.123, 4.5), "Kontrast 5,12:1 · erfüllt (min. 4,50:1)");
  assert.equal(contrastLabel(2.5, 3), "Kontrast 2,50:1 · unter min. 3,00:1");
});

test("contrast label handles decorative and unmeasurable colors", () => {
  assert.equal(contrastLabel(1.2, null), "Kontrast 1,20:1 · dekorativ");
  assert.equal(contrastLabel(null, 4.5), "Kontrast nicht messbar");
});
