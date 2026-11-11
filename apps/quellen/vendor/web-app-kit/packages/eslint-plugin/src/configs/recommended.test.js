import assert from "node:assert/strict";
import path from "node:path";
import { describe, it } from "node:test";
import { Linter } from "eslint";
import plugin from "../index.js";

const linter = new Linter({ configType: "flat" });

const RADIX_IMPORT = 'import { Slot } from "radix-ui";';

function lint(code, fileName) {
  const filePath = path.join(process.cwd(), "src", fileName);
  return linter.verify(code, plugin.configs.recommended, filePath);
}

function lintAt(code, relativePath) {
  const filePath = path.join(process.cwd(), relativePath);
  return linter.verify(code, plugin.configs.recommended, filePath);
}

function ruleIds(messages) {
  return messages.map((message) => message.ruleId);
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

  it("reports foreign UI imports in tool code", () => {
    assert.deepEqual(ruleIds(lint(RADIX_IMPORT, "view.tsx")), ["web-app-kit/no-foreign-ui"]);
  });

  it("reports files under components/ui in tool code", () => {
    const messages = lintAt("export const x = 1;", "src/components/ui/button.tsx");
    assert.deepEqual(ruleIds(messages), ["web-app-kit/no-foreign-ui"]);
  });

  it("exempts the kit package ui folder from no-foreign-ui", () => {
    assert.deepEqual(lintAt(RADIX_IMPORT, "packages/frontend/src/ui/button.tsx"), []);
  });

  it("does not lint the kit inside vendor or node_modules", () => {
    const paths = [
      "vendor/web-app-kit/packages/frontend/src/ui/button.tsx",
      "node_modules/@web-app-kit/frontend/src/ui/button.tsx",
    ];
    paths.forEach((relativePath) => {
      const messages = lintAt(RADIX_IMPORT, relativePath);
      assert.ok(messages.every((message) => message.ruleId === null));
    });
  });
});
