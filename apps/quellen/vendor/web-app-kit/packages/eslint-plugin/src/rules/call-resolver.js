import { lookupBinding } from "./binding-lookup.js";
import { combineAll, kitResolution, unresolvedResolution } from "./resolution.js";

export const CLASS_HELPERS = new Set(["cn", "clsx", "cva"]);

const KIT_CLASS_EXPORT = /(_CLASSES|Variants)$/;

export function resolveKitExport(name) {
  return KIT_CLASS_EXPORT.test(name ?? "") ? kitResolution() : unresolvedResolution();
}

function isWhitespaceSeparator(argumentsList) {
  if (argumentsList.length === 0) return true;
  const [separator] = argumentsList;
  const isStringLiteral = separator.type === "Literal" && typeof separator.value === "string";
  return argumentsList.length === 1 && isStringLiteral && separator.value.trim() === "";
}

function resolveArguments(node, state) {
  return combineAll(node.arguments.map((argument) => state.visit(argument)));
}

function getHelperName(binding, localName) {
  const { importedName } = binding;
  if (!importedName || importedName === "default" || importedName === "*") return localName;
  return importedName;
}

function resolveConstantCallee(binding, state) {
  const { init } = binding;
  const isExpressionArrow =
    init.type === "ArrowFunctionExpression" && init.body.type !== "BlockStatement";
  if (isExpressionArrow) return state.visitBinding(binding.variable, init.body);
  if (init.type === "CallExpression") return state.visitBinding(binding.variable, init);
  return unresolvedResolution();
}

function resolveIdentifierCall(node, state) {
  const binding = lookupBinding(node.callee, state.sourceCode, state.kitModules);
  const isHelper = CLASS_HELPERS.has(getHelperName(binding, node.callee.name));
  if (binding.kind === "kit") {
    return isHelper ? resolveArguments(node, state) : resolveKitExport(binding.importedName);
  }
  if (binding.kind === "constant") return resolveConstantCallee(binding, state);
  return isHelper ? resolveArguments(node, state) : unresolvedResolution();
}

function getPropertyName(member) {
  if (member.computed) return undefined;
  return member.property.name;
}

export function isKitNamespace(node, state) {
  if (node.type !== "Identifier") return false;
  return lookupBinding(node, state.sourceCode, state.kitModules).kind === "kit";
}

function resolveMemberCall(node, state) {
  const { callee } = node;
  const propertyName = getPropertyName(callee);
  if (propertyName === "join") return resolveJoin(node, state);
  if (!isKitNamespace(callee.object, state)) return unresolvedResolution();
  if (CLASS_HELPERS.has(propertyName)) return resolveArguments(node, state);
  return resolveKitExport(propertyName);
}

function resolveJoin(node, state) {
  if (!isWhitespaceSeparator(node.arguments)) return unresolvedResolution();
  return state.visit(node.callee.object);
}

export function resolveCall(node, state) {
  if (node.callee.type === "Identifier") return resolveIdentifierCall(node, state);
  if (node.callee.type === "MemberExpression") return resolveMemberCall(node, state);
  return unresolvedResolution();
}
