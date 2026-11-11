import { interactiveStates } from "./interactive-states.js";
import { createRuleTester } from "./rule-tester.helper.js";

const KIT_UI_IMPORT = 'import { INTERACTIVE_SURFACE_CLASSES, buttonVariants, cn } ' +
  'from "@web-app-kit/frontend/ui";';

createRuleTester().run("interactive-states (valid)", interactiveStates, {
  valid: [
    {
      name: "component with capital letter is not checked",
      code: "const view = <Button onClick={f}>Save</Button>;",
    },
    {
      name: "kit constant imported from @web-app-kit/frontend/ui",
      code: `${KIT_UI_IMPORT}
        const view = <button type="button" className={INTERACTIVE_SURFACE_CLASSES}>x</button>;`,
    },
    {
      name: "kit constant merged with cn",
      code: `${KIT_UI_IMPORT}
        const view = <button className={cn(INTERACTIVE_SURFACE_CLASSES, "w-full")}>x</button>;`,
    },
    {
      name: "buttonVariants call on an anchor",
      code: `${KIT_UI_IMPORT}
        const view = <a href="/x" className={buttonVariants({ variant: "ghost" })}>x</a>;`,
    },
    {
      name: "module constant built with join",
      code: `const ROW_CLASSES = [
          "hover:bg-surface-hover active:translate-y-px",
          "focus-visible:outline-2",
        ].join(" ");
        const view = <button className={ROW_CLASSES}>x</button>;`,
    },
    {
      name: "primary state roles count as visible states",
      code: `const ITEM_CLASSES = [
          "not-disabled:hover:bg-primary-subtle not-disabled:active:inset-ring-primary",
          "aria-pressed:border-primary focus-visible:ring-3",
        ].join(" ");
        const view = <button aria-pressed="true" className={ITEM_CLASSES}>x</button>;`,
    },
    {
      name: "states with prefixed variants and string concatenation",
      code: `const TITLE = "not-disabled:hover:underline " + "not-disabled:active:opacity-80 " +
          "focus-visible:ring-2";
        const view = <button className={TITLE}>x</button>;`,
    },
    {
      name: "arbitrary variants count as states",
      code: `const view = <div role="button" tabIndex={0} onKeyDown={f}
        className="[&:hover]:underline [&:active]:opacity-80 [&:focus-visible]:ring-2" />;`,
    },
    {
      name: "spread attribute with statically satisfied className",
      code: `const view = <button {...props}
        className="hover:bg-surface-hover active:translate-y-px focus-visible:ring-2" />;`,
    },
    {
      name: "ternary with all states in both branches",
      code: `const view = <button className={clsx(
          isOpen
            ? "hover:bg-surface-hover active:translate-y-px focus-visible:ring-2"
            : "hover:bg-surface-selected active:opacity-80 focus-visible:ring-3",
        )}>x</button>;`,
    },
    {
      name: "group button with group-hover and group-active on a descendant",
      code: `const DOT = "group size-6 focus-visible:outline-2";
        const view = <button className={DOT}>
          <span className="group-hover:bg-x group-active:scale-95" />
        </button>;`,
    },
    {
      name: "anchor rendered through a component with asChild",
      code: `const view = <Button variant="ghost" asChild>
          <a href="/x" aria-label="x"><Icon /></a>
        </Button>;`,
    },
    {
      name: "anchor inside an aliased kit Button with asChild",
      code: `import { Button as KitButton } from "@web-app-kit/frontend/ui";
        const view = <KitButton asChild><a href="/x">x</a></KitButton>;`,
    },
    {
      name: "pointer-only drag zone is not an interaction trigger",
      code: 'const view = <div onPointerDown={f} onMouseDown={f} className="cursor-grab" />;',
    },
    { name: "native input with onClick", code: "const view = <input onClick={f} />;" },
    { name: "div without handler", code: 'const view = <div className="cursor-pointer" />;' },
    {
      name: "anchor without href and handler",
      code: 'const view = <a id="anchor">x</a>;',
    },
    {
      name: "local constant inside the component",
      code: `function Row() {
          const classes = \`hover:bg-surface-hover active:translate-y-px focus-visible:ring-2\`;
          return <button className={classes}>x</button>;
        }`,
    },
  ],
  invalid: [],
});
