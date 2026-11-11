import { combineAll, textResolution, unresolvedResolution } from "./resolution.js";

const EXPRESSION_MARKER = "\u0000";

function flattenPlus(node) {
  if (node.type === "BinaryExpression" && node.operator === "+") {
    return [...flattenPlus(node.left), ...flattenPlus(node.right)];
  }
  if (node.type === "TemplateLiteral") return templateParts(node);
  if (node.type === "Literal" && node.value !== null) return [{ text: String(node.value) }];
  return [{ expression: node }];
}

function templateParts(node) {
  return node.quasis.flatMap((quasi, index) => {
    const expression = node.expressions[index];
    const text = { text: quasi.value.cooked ?? "" };
    return expression ? [text, ...flattenPlus(expression)] : [text];
  });
}

function splitByMarker(parts) {
  const text = parts.map((part) => part.text ?? EXPRESSION_MARKER).join("");
  const expressions = parts.filter((part) => part.expression).map((part) => part.expression);
  return { tokens: text.split(/\s+/).filter(Boolean), expressions };
}

function countMarkers(token) {
  return token.split(EXPRESSION_MARKER).length - 1;
}

export function resolveConcatenation(node, visit) {
  const { tokens, expressions } = splitByMarker(flattenPlus(node));
  const resolutions = [];
  const staticTokens = [];
  let expressionIndex = 0;
  for (const token of tokens) {
    const markerCount = countMarkers(token);
    if (token === EXPRESSION_MARKER) resolutions.push(visit(expressions[expressionIndex]));
    else if (markerCount > 0) resolutions.push(unresolvedResolution());
    else staticTokens.push(token);
    expressionIndex += markerCount;
  }
  return combineAll([textResolution(staticTokens.join(" ")), ...resolutions]);
}
