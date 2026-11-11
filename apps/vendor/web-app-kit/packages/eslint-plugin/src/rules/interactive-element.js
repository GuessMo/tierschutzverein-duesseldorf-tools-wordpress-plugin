import { lookupBinding } from "./binding-lookup.js";
import {
  findAttribute,
  getIntrinsicTagName,
  getStaticAttributeValue,
  hasAttribute,
} from "./jsx-attributes.js";

const NATIVE_CONTROL_TAGS = new Set(["input", "select", "textarea", "label", "option"]);

const HANDLER_ATTRIBUTES = ["onClick", "onKeyDown"];

const SLOT_COMPONENTS = new Set(["Button", "Card", "Badge"]);

const INTERACTIVE_ROLES = new Set([
  "button",
  "tab",
  "menuitem",
  "menuitemcheckbox",
  "menuitemradio",
  "option",
  "switch",
  "checkbox",
  "radio",
  "link",
]);

export function isInteractiveElement(openingElement) {
  const tagName = getIntrinsicTagName(openingElement);
  if (!tagName || NATIVE_CONTROL_TAGS.has(tagName)) return false;
  if (tagName === "button") return true;
  if (tagName === "a" && hasAttribute(openingElement, "href")) return true;
  if (HANDLER_ATTRIBUTES.some((name) => hasAttribute(openingElement, name))) return true;
  return INTERACTIVE_ROLES.has(getStaticAttributeValue(openingElement, "role"));
}

const PASS_THROUGH_PARENTS = new Set([
  "JSXExpressionContainer",
  "ConditionalExpression",
  "LogicalExpression",
]);

function getSlotComponentName(name, sourceCode, kitModules) {
  if (name.type === "JSXMemberExpression") return name.property.name;
  if (name.type !== "JSXIdentifier") return undefined;
  if (SLOT_COMPONENTS.has(name.name)) return name.name;
  const binding = lookupBinding(name, sourceCode, kitModules);
  return binding.kind === "kit" ? binding.importedName : undefined;
}

function isAttributeEnabled(openingElement, attributeName) {
  const attribute = findAttribute(openingElement, attributeName);
  if (!attribute) return false;
  if (!attribute.value) return true;
  return getStaticAttributeValue(openingElement, attributeName) === true;
}

function isStylingSlot(parentOpening, sourceCode, kitModules) {
  const componentName = getSlotComponentName(parentOpening.name, sourceCode, kitModules);
  if (!SLOT_COMPONENTS.has(componentName)) return false;
  return componentName !== "Card" || isAttributeEnabled(parentOpening, "interactive");
}

function findParentElement(element) {
  let current = element.parent;
  while (current && PASS_THROUGH_PARENTS.has(current.type)) current = current.parent;
  return current?.type === "JSXElement" ? current : undefined;
}

export function isSlotChild(openingElement, sourceCode, kitModules) {
  const parentOpening = findParentElement(openingElement.parent)?.openingElement;
  if (!parentOpening || getIntrinsicTagName(parentOpening)) return false;
  if (!isAttributeEnabled(parentOpening, "asChild")) return false;
  return isStylingSlot(parentOpening, sourceCode, kitModules);
}
