import { interactiveStates } from "./interactive-states.js";
import { createRuleTester } from "./rule-tester.helper.js";

const ALL = "hover:bg-x active:y focus-visible:z";
const KIT = 'import { DropdownMenuTrigger, TooltipTrigger, useTheme, cn } ' +
  'from "@web-app-kit/frontend/ui";';
const UNRESOLVED = [{ messageId: "unresolved" }];

function missing(states) {
  return [{ messageId: "missingStates", data: { states } }];
}

function button(className) {
  return `const view = <button className={${className}}>x</button>;`;
}

function groupButton(children) {
  return `const view = <button className="group focus-visible:ring-2">${children}</button>;`;
}

const GROUP_CHILD = '<span className="group-hover:bg-x group-active:y" />';
const OTHER_GROUP_CHILD = '<span className="group-hover:bg-w group-active:v" />';

createRuleTester().run("interactive-states (review)", interactiveStates, {
  valid: [
    {
      name: "STRETCHED_LINK_CLASSES and badgeVariants from the kit",
      code: `import { STRETCHED_LINK_CLASSES, badgeVariants } from "@web-app-kit/frontend/ui";
        const view = <a href="/x" className={STRETCHED_LINK_CLASSES}>x</a>;
        const badge = <a href="/y" className={badgeVariants({ variant: "outline" })}>y</a>;`,
    },
    {
      name: "group descendants in both ternary branches",
      code: groupButton(`{isOpen ? ${GROUP_CHILD} : ${OTHER_GROUP_CHILD}}`),
    },
    {
      name: "nested ternary with all states in every branch",
      code: button(`a ? "${ALL}" : b ? "${ALL} px-2" : "${ALL} px-3"`),
    },
    {
      name: "cn with base classes and a ternary carrying the states",
      code: button(`cn("px-2", isOpen ? "${ALL}" : "${ALL} opacity-80")`),
    },
    {
      name: "join without separator",
      code: `const CLASSES = ["${ALL}"].join(); ${button("CLASSES")}`,
    },
  ],
  invalid: [
    {
      name: "kit DropdownMenuTrigger does not style its child",
      code: `${KIT} const view = <DropdownMenuTrigger asChild>
        <button type="button" className="px">x</button></DropdownMenuTrigger>;`,
      errors: missing("hover, active, focus-visible"),
    },
    {
      name: "kit TooltipTrigger does not style its child",
      code: `${KIT} const view = <TooltipTrigger asChild><a href="/x">x</a></TooltipTrigger>;`,
      errors: missing("hover, active, focus-visible"),
    },
    {
      name: "kit hook call is no class export",
      code: `${KIT} ${button("useTheme()")}`,
      errors: UNRESOLVED,
    },
    {
      name: "kit constant without classes",
      code: `import { APP_SHELL_CONTENT_ID } from "@web-app-kit/frontend/ui";
        ${button("APP_SHELL_CONTENT_ID")}`,
      errors: UNRESOLVED,
    },
    {
      name: "group marker behind && does not make a group",
      code: `const view = <button className={cn("focus-visible:ring-2", isOpen && "group")}>
        ${GROUP_CHILD}</button>;`,
      errors: missing("hover, active"),
    },
    {
      name: "group descendant rendered behind &&",
      code: groupButton(`{isOpen && ${GROUP_CHILD}}`),
      errors: missing("hover, active"),
    },
    {
      name: "group descendant in a single ternary branch",
      code: groupButton(`{isOpen ? ${GROUP_CHILD} : <span />}`),
      errors: missing("hover, active"),
    },
    {
      name: "cn with states in one ternary branch only",
      code: button(`cn("px-2", isOpen ? "${ALL}" : "opacity-80")`),
      errors: missing("hover, active, focus-visible"),
    },
    {
      name: "nested ternary with one branch lacking states",
      code: button(`a ? "${ALL}" : b ? "${ALL}" : "px-3"`),
      errors: missing("hover, active, focus-visible"),
    },
    {
      name: "arrow function with block body",
      code: `const getClasses = () => { return "${ALL}"; }; ${button("getClasses()")}`,
      errors: UNRESOLVED,
    },
    {
      name: "recursive arrow function",
      code: `const getClasses = () => getClasses(); ${button("getClasses()")}`,
      errors: UNRESOLVED,
    },
    {
      name: "mutually recursive arrow functions",
      code: `const first = () => second(); const second = () => first();
        ${button("first()")}`,
      errors: UNRESOLVED,
    },
    {
      name: "join with a non-whitespace separator",
      code: `const CLASSES = ["hover:bg-x", "active:y focus-visible:z"].join(",");
        ${button("CLASSES")}`,
      errors: UNRESOLVED,
    },
  ],
});
