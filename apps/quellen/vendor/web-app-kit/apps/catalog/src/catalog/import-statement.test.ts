import assert from "node:assert/strict";
import { test } from "node:test";
import { importStatement } from "./import-statement";

test("short import stays on one line", () => {
  assert.equal(
    importStatement({ exports: ["Button"] }),
    'import { Button } from "@web-app-kit/frontend/ui";',
  );
});

test("long import wraps one name per line and adds type import", () => {
  const statement = importStatement({
    exports: ["Card", "CardHeader", "CardTitle", "CardDescription", "CardContent"],
    types: ["CardProps"],
  });
  assert.match(statement, /^import \{\n {2}Card,\n/);
  assert.match(statement, /\nimport type \{ CardProps \} from "@web-app-kit\/frontend\/ui";$/);
});

test("an entry with its own import path imports from there", () => {
  assert.equal(
    importStatement({ exports: ["Command"], importPath: "@web-app-kit/frontend/command" }),
    'import { Command } from "@web-app-kit/frontend/command";',
  );
});
