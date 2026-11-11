import { interactiveStates } from "./interactive-states.js";
import { createRuleTester } from "./rule-tester.helper.js";

const ALL_STATES = "hover, active, focus-visible";

function missing(states) {
  return { messageId: "missingStates", data: { states } };
}

createRuleTester().run("interactive-states (invalid)", interactiveStates, {
  valid: [],
  invalid: [
    {
      name: "LeftSidebar tab button with cn and conditional classes",
      code: `import { cn } from "@/lib/utils";
        const view = <button type="button" onClick={f} className={cn(
          "flex-1 border-b-2 py-2 text-sm",
          tab === "views" && "border-foreground",
        )}>x</button>;`,
      errors: [missing(ALL_STATES)],
    },
    {
      name: "cn from the kit does not count as kit coverage",
      code: `import { cn } from "@web-app-kit/frontend/ui";
        const view = <button className={cn("px-2")}>x</button>;`,
      errors: [missing(ALL_STATES)],
    },
    {
      name: "clickable div with cursor only",
      code: 'const view = <div onClick={f} className="cursor-pointer" />;',
      errors: [missing(ALL_STATES)],
    },
    {
      name: "button with hover only",
      code: 'const view = <button className="hover:bg-foreground/6">x</button>;',
      errors: [missing("active, focus-visible")],
    },
    {
      name: "module constant with hover only",
      code: `const TOOL_BUTTON_CLASS = "hover:bg-x";
        const view = <button className={TOOL_BUTTON_CLASS}>x</button>;`,
      errors: [missing("active, focus-visible")],
    },
    {
      name: "constant imported from another app module",
      code: `import { TOOL_BUTTON_CLASS } from "./toolClasses";
        const view = <button className={TOOL_BUTTON_CLASS}>x</button>;`,
      errors: [{ messageId: "unresolved" }],
    },
    {
      name: "className from props",
      code: "const view = <button className={props.className}>x</button>;",
      errors: [{ messageId: "unresolved" }],
    },
    {
      name: "spread attribute without className",
      code: "const view = <button {...props}>x</button>;",
      errors: [{ messageId: "unresolved" }],
    },
    {
      name: "anchor with href without className",
      code: 'const view = <a href="/x">x</a>;',
      errors: [missing(ALL_STATES)],
    },
    {
      name: "role tab without states",
      code: 'const view = <span role="tab" className="px-2">x</span>;',
      errors: [missing(ALL_STATES)],
    },
    {
      name: "group button without group-active on descendants",
      code: `const view = <button className="group focus-visible:ring-2">
          <span className="group-hover:bg-x hover:scale-95" />
        </button>;`,
      errors: [missing("active")],
    },
    {
      name: "button inside DropdownMenuTrigger with asChild",
      code: `import { DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
        const view = <DropdownMenuTrigger asChild><button type="button">x</button>
        </DropdownMenuTrigger>;`,
      errors: [missing(ALL_STATES)],
    },
    {
      name: "hover:bg-muted on a satisfied button",
      code: `const view = <button
        className="hover:bg-muted active:translate-y-px focus-visible:ring-2">x</button>;`,
      errors: [{ messageId: "invisibleHover", data: { token: "hover:bg-muted" } }],
    },
    {
      name: "prefixed hover:bg-accent with opacity on a non-interactive element",
      code: 'const view = <div className="not-disabled:hover:bg-accent/50" />;',
      errors: [
        {
          messageId: "invisibleHover",
          data: { token: "not-disabled:hover:bg-accent/50" },
        },
      ],
    },
  ],
});
