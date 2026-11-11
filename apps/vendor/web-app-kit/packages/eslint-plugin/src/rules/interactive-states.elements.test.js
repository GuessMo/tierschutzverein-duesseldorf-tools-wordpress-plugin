import { interactiveStates } from "./interactive-states.js";
import { createRuleTester } from "./rule-tester.helper.js";

const ALL_STATES = "hover, active, focus-visible";

function missing(states) {
  return [{ messageId: "missingStates", data: { states } }];
}

function invisible(token) {
  return [{ messageId: "invisibleHover", data: { token } }];
}

createRuleTester().run("interactive-states (elements)", interactiveStates, {
  valid: [
    { name: "select with onClick", code: "const view = <select onClick={f} />;" },
    { name: "textarea with onClick", code: "const view = <textarea onClick={f} />;" },
    { name: "label with onClick", code: "const view = <label onClick={f}>x</label>;" },
    { name: "option with onClick", code: "const view = <option onClick={f}>x</option>;" },
    {
      name: "kit Button through a namespace with asChild",
      code: `import * as Kit from "@web-app-kit/frontend/ui";
        const view = <Kit.Button asChild><a href="/x">x</a></Kit.Button>;`,
    },
    {
      name: "asChild child inside a conditional",
      code: `const view = <Button asChild>{isLocal ? <a href="/x">x</a> : <a href="/y">y</a>}
        </Button>;`,
    },
    {
      name: "interactive Card with asChild",
      code: 'const view = <Card interactive asChild><a href="/x">x</a></Card>;',
    },
    {
      name: "kit Card with interactive={true} and asChild",
      code: `import { Card as KitCard } from "@web-app-kit/frontend/ui";
        const view = <KitCard interactive={true} asChild>
          <button type="button">x</button>
        </KitCard>;`,
    },
    {
      name: "named group matched by named group variants",
      code: `const view = <button className="group/row focus-visible:ring-2">
          <span className="group-hover/row:bg-x group-active/row:scale-95" />
        </button>;`,
    },
  ],
  invalid: [
    {
      name: "onKeyDown alone",
      code: 'const view = <div onKeyDown={f} tabIndex={0} className="px-2" />;',
      errors: missing(ALL_STATES),
    },
    {
      name: "anchor with onClick and without href",
      code: 'const view = <a onClick={f} className="px-2">x</a>;',
      errors: missing(ALL_STATES),
    },
    {
      name: "asChild={false} does not delegate",
      code: 'const view = <Button asChild={false}><a href="/x">x</a></Button>;',
      errors: missing(ALL_STATES),
    },
    {
      name: "Card with asChild but without interactive",
      code: 'const view = <Card asChild><a href="/x">x</a></Card>;',
      errors: missing(ALL_STATES),
    },
    {
      name: "Card with interactive={false}",
      code: 'const view = <Card interactive={false} asChild><a href="/x">x</a></Card>;',
      errors: missing(ALL_STATES),
    },
    {
      name: "group variants on the element itself do not count",
      code: `const view = <button
        className="group-hover:bg-x group-active:y group-focus-visible:z">x</button>;`,
      errors: missing(ALL_STATES),
    },
    {
      name: "named group variants of another group do not count",
      code: `const view = <button className="group/a focus-visible:ring-2">
          <span className="group-hover/b:bg-x group-active/b:y" />
        </button>;`,
      errors: missing("hover, active"),
    },
    {
      name: "nested group of the same name stops the walk",
      code: `const view = <button className="group focus-visible:ring-2">
          <span className="group"><span className="group-hover:bg-x group-active:y" /></span>
        </button>;`,
      errors: missing("hover, active"),
    },
    {
      name: "unresolved class of a group descendant",
      code: `const view = <button className="group focus-visible:ring-2">
          <span className={props.iconClassName} />
        </button>;`,
      errors: [{ messageId: "unresolved" }],
    },
    {
      name: "group-hover:bg-muted",
      code: 'const view = <div className="group-hover:bg-muted" />;',
      errors: invisible("group-hover:bg-muted"),
    },
    {
      name: "arbitrary hover with bg-accent",
      code: 'const view = <div className="[&:hover]:bg-accent/40" />;',
      errors: invisible("[&:hover]:bg-accent/40"),
    },
    {
      name: "important modifiers on bg-muted",
      code: 'const view = <div className="hover:!bg-muted hover:bg-muted-foreground" />;',
      errors: invisible("hover:!bg-muted"),
    },
    {
      name: "invisible hover in a conditional branch",
      code: 'const view = <div className={isOpen ? "px-2" : "hover:bg-muted/50"} />;',
      errors: invisible("hover:bg-muted/50"),
    },
  ],
});
