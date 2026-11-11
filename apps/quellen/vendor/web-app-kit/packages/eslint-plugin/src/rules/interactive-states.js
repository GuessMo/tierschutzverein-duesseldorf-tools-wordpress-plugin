import { resolveClassName } from "./class-name-resolver.js";
import { collectGroupStates } from "./group-descendants.js";
import { isInteractiveElement, isSlotChild } from "./interactive-element.js";
import { findAttribute, hasSpreadAttribute } from "./jsx-attributes.js";
import { emptyResolution } from "./resolution.js";
import { REQUIRED_STATES, findInvisibleHoverTokens, getGroupNames } from "./state-tokens.js";

const DEFAULT_KIT_MODULES = ["@web-app-kit/frontend", "@web-app-kit/frontend/ui"];

const MESSAGES = {
  missingStates:
    "Interaktives Element ohne sichtbare Zustände: es fehlen {{states}}. Kit-Komponenten " +
    "(Button, INTERACTIVE_SURFACE_CLASSES) oder hover:-, active:- und focus-visible:-Klassen " +
    "nutzen.",
  unresolved:
    "className kann nicht statisch geprüft werden; Kit-Klassen oder Konstante im selben " +
    "Modul nutzen.",
  invisibleHover:
    "{{token}} ist unsichtbar auf Kit-Flächen; bg-primary-subtle, overlay-hover bzw. " +
    "bg-surface-hover nutzen.",
};

function resolveAttribute(attribute, sourceCode, kitModules) {
  if (!attribute?.value) return emptyResolution();
  return resolveClassName(attribute.value, sourceCode, kitModules);
}

function reportInvisibleHover(context, attribute, tokens) {
  findInvisibleHoverTokens(tokens).forEach((token) =>
    context.report({ node: attribute, messageId: "invisibleHover", data: { token } }),
  );
}

function collectGroupResult(node, resolution, resolver) {
  const groupNames = getGroupNames(resolution.keys);
  return collectGroupStates(node.parent, groupNames, resolver);
}

function findMissingStates(node, resolution, resolver) {
  const groupResult = collectGroupResult(node, resolution, resolver);
  const missingStates = REQUIRED_STATES.filter(
    (state) => !resolution.keys.has(state) && !groupResult.states.has(state),
  );
  return { missingStates, isUnresolved: resolution.isUnresolved || groupResult.isUnresolved };
}

function reportMissingStates(context, node, resolution, resolver) {
  if (resolution.isKitCovered) return;
  if (isSlotChild(node, resolver.sourceCode, resolver.kitModules)) return;
  const { missingStates, isUnresolved } = findMissingStates(node, resolution, resolver);
  if (missingStates.length === 0) return;
  if (isUnresolved || hasSpreadAttribute(node)) {
    context.report({ node, messageId: "unresolved" });
    return;
  }
  context.report({
    node,
    messageId: "missingStates",
    data: { states: missingStates.join(", ") },
  });
}

export const interactiveStates = {
  meta: {
    type: "problem",
    docs: {
      description:
        "Interactive elements need hover, active and focus-visible states or kit classes.",
    },
    schema: [
      {
        type: "object",
        properties: { kitModules: { type: "array", items: { type: "string" } } },
        additionalProperties: false,
      },
    ],
    defaultOptions: [{ kitModules: DEFAULT_KIT_MODULES }],
    messages: MESSAGES,
  },
  create(context) {
    const [{ kitModules }] = context.options;
    const { sourceCode } = context;
    const resolver = {
      sourceCode,
      kitModules,
      visitorKeys: sourceCode.visitorKeys,
      resolve: (attribute) => resolveAttribute(attribute, sourceCode, kitModules),
    };
    return {
      JSXOpeningElement(node) {
        const attribute = findAttribute(node, "className");
        const resolution = resolver.resolve(attribute);
        if (attribute) reportInvisibleHover(context, attribute, resolution.tokens);
        if (!isInteractiveElement(node)) return;
        reportMissingStates(context, node, resolution, resolver);
      },
    };
  },
};
