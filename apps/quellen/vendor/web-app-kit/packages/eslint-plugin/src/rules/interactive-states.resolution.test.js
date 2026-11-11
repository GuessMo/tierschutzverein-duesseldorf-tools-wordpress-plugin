import { interactiveStates } from "./interactive-states.js";
import { createRuleTester } from "./rule-tester.helper.js";

const ALL = "hover:bg-x active:y focus-visible:z";
const UNRESOLVED = [{ messageId: "unresolved" }];

function missing(states) {
  return [{ messageId: "missingStates", data: { states } }];
}

function button(className) {
  return `const view = <button className={${className}}>x</button>;`;
}

createRuleTester().run("interactive-states (resolution)", interactiveStates, {
  valid: [
    {
      name: "custom kitModules option",
      code: `import { SURFACE_CLASSES } from "@acme/ui"; ${button("SURFACE_CLASSES")}`,
      options: [{ kitModules: ["@acme/ui"] }],
    },
    {
      name: "namespace import from the kit",
      code: `import * as Kit from "@web-app-kit/frontend/ui";
        ${button("Kit.INTERACTIVE_SURFACE_CLASSES")}`,
    },
    {
      name: "local arrow function with expression body",
      code: `const getClasses = () => "${ALL}"; ${button("getClasses()")}`,
    },
    {
      name: "local cva factory",
      code: `const variants = cva("${ALL}", { variants: { size: { sm: "h-7" } } });
        ${button("cn(variants({ size }))")}`,
    },
    {
      name: "template literal with a standalone expression",
      code: `const BASE = "hover:bg-x active:y"; ${button("`${BASE} focus-visible:z`")}`,
    },
    {
      name: "?? with all states on both sides",
      code: `const BASE = "${ALL}"; ${button(`BASE ?? "${ALL}"`)}`,
    },
    {
      name: "aliased clsx import",
      code: `import { clsx as merge } from "clsx"; ${button(`merge("${ALL}")`)}`,
    },
  ],
  invalid: [
    {
      name: "states only behind &&",
      code: button(`isOpen && "${ALL}"`),
      errors: missing("hover, active, focus-visible"),
    },
    {
      name: "states only in one ternary branch",
      code: button(`isOpen ? "${ALL}" : "px-2"`),
      errors: missing("hover, active, focus-visible"),
    },
    {
      name: "states only behind || and ??",
      code: button(`(label || "${ALL}") + " " + (size ?? "${ALL}")`),
      errors: UNRESOLVED,
    },
    {
      name: "states only in clsx object keys",
      code: button(`clsx("hover:bg-x", { "active:y focus-visible:z": isOpen })`),
      errors: missing("active, focus-visible"),
    },
    {
      name: "states only on the right side of ||",
      code: button(`"px-2" || "${ALL}"`),
      errors: missing("hover, active, focus-visible"),
    },
    {
      name: "kit cn as a value is no kit coverage",
      code: `import * as Kit from "@web-app-kit/frontend/ui"; ${button("Kit.cn")}`,
      errors: UNRESOLVED,
    },
    {
      name: "constant cycle",
      code: `const first = second; const second = first; ${button("first")}`,
      errors: UNRESOLVED,
    },
    {
      name: "self-referencing constant",
      code: `const classes = classes + " px"; ${button("classes")}`,
      errors: UNRESOLVED,
    },
    {
      name: "call of a function declared elsewhere",
      code: `import { toolClasses } from "./tools"; ${button("toolClasses()")}`,
      errors: UNRESOLVED,
    },
    {
      name: "local function declaration is not evaluated",
      code: `function getClasses() { return "${ALL}"; } ${button("getClasses()")}`,
      errors: UNRESOLVED,
    },
    {
      name: "kit cn through a namespace evaluates its arguments",
      code: `import * as Kit from "@web-app-kit/frontend/ui"; ${button('Kit.cn("px-2")')}`,
      errors: missing("hover, active, focus-visible"),
    },
    {
      name: "template expression glued to a token",
      code: button("`hover:${color} active:y focus-visible:z`"),
      errors: UNRESOLVED,
    },
    {
      name: "variants without utility",
      code: 'const view = <button className="hover: active: focus-visible:">x</button>;',
      errors: missing("hover, active, focus-visible"),
    },
    {
      name: "constant shadowed by a parameter",
      code: `const C = "${ALL}"; function Row({ C }) { return <button className={C}>x</button>; }`,
      errors: UNRESOLVED,
    },
    {
      name: "constant shadowed by let",
      code: `const C = "${ALL}";
        function Row() { let C = "px"; return <button className={C}>x</button>; }`,
      errors: UNRESOLVED,
    },
    {
      name: "arbitrary variants inside :not() do not count",
      code: button('"[&:not(:hover)]:bg-x [&:not(:active)]:y [&:not(:focus-visible)]:z"'),
      errors: missing("hover, active, focus-visible"),
    },
  ],
});
