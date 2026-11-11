export function findAttribute(openingElement, name) {
  return openingElement.attributes.find(
    (attribute) =>
      attribute.type === "JSXAttribute" &&
      attribute.name.type === "JSXIdentifier" &&
      attribute.name.name === name,
  );
}

export function hasAttribute(openingElement, name) {
  return findAttribute(openingElement, name) !== undefined;
}

export function hasSpreadAttribute(openingElement) {
  return openingElement.attributes.some((attribute) => attribute.type === "JSXSpreadAttribute");
}

export function getStaticAttributeValue(openingElement, name) {
  const value = findAttribute(openingElement, name)?.value;
  if (!value) return undefined;
  if (value.type === "Literal") return value.value;
  if (value.type !== "JSXExpressionContainer") return undefined;
  return getStaticExpressionValue(value.expression);
}

export function getIntrinsicTagName(openingElement) {
  const { name } = openingElement;
  if (name.type !== "JSXIdentifier") return undefined;
  return /^[a-z]/.test(name.name) ? name.name : undefined;
}

function getStaticExpressionValue(expression) {
  if (expression.type === "Literal") return expression.value;
  const isStaticTemplate = expression.type === "TemplateLiteral" && !expression.expressions.length;
  return isStaticTemplate ? expression.quasis[0].value.cooked : undefined;
}
