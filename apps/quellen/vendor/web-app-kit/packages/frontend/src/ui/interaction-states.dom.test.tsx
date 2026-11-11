import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./dom-env.helper.js";

installDomEnvironment();

const { cleanup, render, screen } = await import("@testing-library/react");
const ui = await import("./index.js");

test.afterEach(cleanup);

function classesOf(element: Element | null) {
  return element?.getAttribute("class")?.split(" ") ?? [];
}

function assertClasses(classes: string[], expected: string[], label: string) {
  for (const token of expected) assert.ok(classes.includes(token), `${label}: ${token}`);
}

const PRESSED_CLASSES = [
  "not-disabled:hover:bg-primary-subtle",
  "not-disabled:hover:text-primary-subtle-foreground",
  "not-disabled:active:border-primary",
  "not-disabled:active:inset-ring-primary",
  "aria-pressed:border-primary",
  "aria-pressed:bg-primary-subtle",
  "aria-pressed:inset-ring-1",
  "aria-expanded:inset-ring-primary",
];

test("default Button steps through primary hover and active roles", () => {
  const classes = ui.buttonVariants({ variant: "default" }).split(" ");
  assertClasses(
    classes,
    ["not-disabled:hover:bg-primary-hover", "not-disabled:active:bg-primary-active"],
    "default",
  );
  assert.equal(classes.some((token) => token.includes("bg-primary/")), false);
});

test("ghost and outline Buttons hover subtle and mark pressed with border and ring", () => {
  for (const variant of ["ghost", "outline"] as const) {
    render(
      <ui.Button variant={variant} aria-pressed="true">
        {variant}
      </ui.Button>,
    );
    const classes = classesOf(screen.getByRole("button", { name: variant }));
    assertClasses(classes, PRESSED_CLASSES, variant);
    assert.equal(classes.some((token) => token.includes("surface-")), false, variant);
  }
});

test("link Button and link Badge switch to the primary hover text", () => {
  assertClasses(
    ui.buttonVariants({ variant: "link" }).split(" "),
    ["text-primary", "not-disabled:hover:text-primary-hover", "not-disabled:hover:underline"],
    "button link",
  );
  assertClasses(
    ui.badgeVariants({ variant: "link" }).split(" "),
    ["text-primary", "hover:text-primary-hover", "hover:underline"],
    "badge link",
  );
  assertClasses(
    ui.badgeVariants({ variant: "default" }).split(" "),
    ["[a]:hover:bg-primary-hover", "[a]:active:bg-primary-active"],
    "badge default",
  );
});

test("menu items focus on the subtle primary surface with a primary check", () => {
  render(
    <ui.DropdownMenu open>
      <ui.DropdownMenuTrigger>Menu</ui.DropdownMenuTrigger>
      <ui.DropdownMenuContent>
        <ui.DropdownMenuItem>Open</ui.DropdownMenuItem>
        <ui.DropdownMenuCheckboxItem checked>Grid</ui.DropdownMenuCheckboxItem>
      </ui.DropdownMenuContent>
    </ui.DropdownMenu>,
  );
  const item = screen.getByRole("menuitem", { name: "Open" });
  const checkbox = screen.getByRole("menuitemcheckbox", { name: "Grid" });
  const expected = ["focus:bg-primary-subtle", "focus:text-primary-subtle-foreground"];
  assertClasses(classesOf(item), expected, "item");
  assertClasses(classesOf(checkbox), expected, "checkbox item");
  const indicator = checkbox.querySelector("[data-slot$=indicator]");
  assert.ok(classesOf(indicator).includes("text-primary"));
  assert.ok(indicator?.querySelector("svg"), "check icon rendered");
});
