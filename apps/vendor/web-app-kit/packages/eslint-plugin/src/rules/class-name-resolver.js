import { lookupBinding } from "./binding-lookup.js";
import { isKitNamespace, resolveCall, resolveKitExport } from "./call-resolver.js";
import { resolveConcatenation } from "./concatenation.js";
import {
  asConditional,
  combineAll,
  combineAlternatives,
  emptyResolution,
  textResolution,
  unresolvedResolution,
} from "./resolution.js";

const WRAPPER_TYPES = new Set([
  "JSXExpressionContainer",
  "TSAsExpression",
  "TSSatisfiesExpression",
  "TSNonNullExpression",
  "TSTypeAssertion",
  "ChainExpression",
]);

const STATIC_VALUE_TYPES = new Set([
  "Literal",
  "TemplateLiteral",
  "ArrayExpression",
  "ObjectExpression",
]);

const HANDLERS = {
  Literal: resolveLiteral,
  TemplateLiteral: (node, state) => resolveConcatenation(node, state.visit),
  BinaryExpression: resolveBinary,
  CallExpression: (node, state) => resolveCall(node, state),
  LogicalExpression: resolveLogical,
  ConditionalExpression: (node, state) =>
    combineAlternatives([state.visit(node.consequent), state.visit(node.alternate)]),
  ArrayExpression: (node, state) => combineAll(node.elements.filter(Boolean).map(state.visit)),
  SpreadElement: (node, state) => state.visit(node.argument),
  ObjectExpression: resolveObject,
  Identifier: resolveIdentifier,
  MemberExpression: resolveMember,
  JSXEmptyExpression: emptyResolution,
};

export function resolveClassName(expression, sourceCode, kitModules) {
  const state = { sourceCode, kitModules, inProgress: new Set() };
  state.visit = (node) => visit(node, state);
  state.visitBinding = (variable, node) => visitBinding(variable, node, state);
  return visit(expression, state);
}

function visit(node, state) {
  if (WRAPPER_TYPES.has(node.type)) return visit(node.expression, state);
  const handler = HANDLERS[node.type];
  return handler ? handler(node, state) : unresolvedResolution();
}

function visitBinding(variable, node, state) {
  if (state.inProgress.has(variable)) return unresolvedResolution();
  state.inProgress.add(variable);
  const resolution = visit(node, state);
  state.inProgress.delete(variable);
  return resolution;
}

function resolveLiteral(node) {
  return typeof node.value === "string" ? textResolution(node.value) : emptyResolution();
}

function resolveBinary(node, state) {
  if (node.operator !== "+") return emptyResolution();
  return resolveConcatenation(node, state.visit);
}

function resolveLogical(node, state) {
  if (node.operator === "&&") return asConditional(state.visit(node.right));
  return combineAlternatives([state.visit(node.left), state.visit(node.right)]);
}

function resolvePropertyKey(property, state) {
  if (property.computed) return state.visit(property.key);
  if (property.key.type === "Identifier") return textResolution(property.key.name);
  return resolveLiteral(property.key);
}

function resolveProperty(property, state) {
  if (property.type === "SpreadElement") return state.visit(property.argument);
  const key = resolvePropertyKey(property, state);
  if (!STATIC_VALUE_TYPES.has(property.value.type)) return asConditional(key);
  return asConditional(combineAll([key, state.visit(property.value)]));
}

function resolveObject(node, state) {
  return combineAll(node.properties.map((property) => resolveProperty(property, state)));
}

function resolveIdentifier(node, state) {
  if (node.name === "undefined") return emptyResolution();
  const binding = lookupBinding(node, state.sourceCode, state.kitModules);
  if (binding.kind === "kit") return resolveKitExport(binding.importedName);
  if (binding.kind !== "constant") return unresolvedResolution();
  return visitBinding(binding.variable, binding.init, state);
}

function resolveMember(node, state) {
  if (!isKitNamespace(node.object, state) || node.computed) return unresolvedResolution();
  return resolveKitExport(node.property.name);
}
