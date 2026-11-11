import { findAttribute } from "./jsx-attributes.js";
import {
  asConditional,
  combineAll,
  combineAlternatives,
  emptyResolution,
} from "./resolution.js";
import { REQUIRED_STATES, groupKey, groupMarkerKey } from "./state-tokens.js";

function isNode(value) {
  return Boolean(value) && typeof value.type === "string";
}

function getChildNodes(node, visitorKeys) {
  return (visitorKeys[node.type] ?? []).flatMap((key) => node[key]).filter(isNode);
}

function resolveElement(element, resolve) {
  const attribute = findAttribute(element.openingElement, "className");
  return attribute ? resolve(attribute) : emptyResolution();
}

function walkElement(element, context) {
  const own = resolveElement(element, context.resolve);
  if (own.keys.has(groupMarkerKey(context.name))) return own;
  return combineAll([own, ...element.children.map((child) => walk(child, context))]);
}

function walkLogical(node, context) {
  const right = walk(node.right, context);
  if (node.operator === "&&") return asConditional(right);
  return combineAlternatives([walk(node.left, context), right]);
}

function walk(node, context) {
  if (node.type === "JSXElement") return walkElement(node, context);
  if (node.type === "LogicalExpression") return walkLogical(node, context);
  if (node.type === "ConditionalExpression") {
    return combineAlternatives([walk(node.consequent, context), walk(node.alternate, context)]);
  }
  const children = getChildNodes(node, context.visitorKeys);
  return combineAll(children.map((child) => walk(child, context)));
}

function collectForGroup(element, name, options) {
  const context = { ...options, name };
  const resolution = combineAll(element.children.map((child) => walk(child, context)));
  const states = REQUIRED_STATES.filter((state) => resolution.keys.has(groupKey(name, state)));
  return { states, isUnresolved: resolution.isUnresolved };
}

export function collectGroupStates(element, groupNames, options) {
  const results = groupNames.map((name) => collectForGroup(element, name, options));
  return {
    states: new Set(results.flatMap((result) => result.states)),
    isUnresolved: results.some((result) => result.isUnresolved),
  };
}
