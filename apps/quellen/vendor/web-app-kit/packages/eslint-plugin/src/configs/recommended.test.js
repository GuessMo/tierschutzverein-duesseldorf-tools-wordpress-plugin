import assert from "node:assert/strict";
import path from "node:path";
import { describe, it } from "node:test";
import { Linter } from "eslint";
import plugin from "../index.js";

const linter = new Linter({ configType: "flat" });

function lint(code, fileName) {
  const filePath = path.join(process.cwd(), "src", fileName);
  return linter.verify(code, plugin.configs.recommended, filePath);
}

describe("configs.recommended", () => {
  it("parses generic arrows and angle-bracket assertions in .ts files", () => {
    const code = "export const id = <T>(x: T): T => x;\nconst n = <number>value;";
    assert.deepEqual(lint(code, "generic.ts"), []);
  });

  it("parses JSX and checks interactive states in .tsx files", () => {
    const code = 'export const view = <button type="button" className="px-2">x</button>;';
    const ruleIds = lint(code, "view.tsx").map((message) => message.ruleId);
    assert.deepEqual(ruleIds, ["web-app-kit/interactive-states"]);
  });

  it("parses class and member decorators in .ts files", () => {
    const code = "@sealed\nexport class Store { @observable value = 1; @action set() {} }";
    assert.deepEqual(lint(code, "store.ts"), []);
  });

  it("parses class decorators in .tsx files", () => {
    const code = "@register\nexport class View { render() { return <div />; } }";
    assert.deepEqual(lint(code, "view-class.tsx"), []);
  });

  it("parses generic arrows with a trailing comma in .tsx files", () => {
    assert.deepEqual(lint("export const id = <T,>(x: T): T => x;", "generic.tsx"), []);
  });
});
