export const REQUIRED_STATES = ["hover", "active", "focus-visible"];

const ARBITRARY_STATE_VARIANT = /^\[&:(hover|active|focus-visible)\]$/;
const GROUP_STATE_VARIANT = /^group-(hover|active|focus-visible)(?:\/(\S+))?$/;
const GROUP_MARKER = /^group(?:\/(\S+))?$/;
const HOVER_VARIANT = /^(hover|group-hover(\/\S+)?|\[&:hover\])$/;
const INVISIBLE_HOVER_UTILITY = /^!?bg-(muted|accent)(\/\S+)?!?$/;

const OPENING_BRACKETS = new Set(["[", "("]);
const CLOSING_BRACKETS = new Set(["]", ")"]);

export function splitVariants(token) {
  const parts = [];
  let depth = 0;
  let current = "";
  for (const character of token) {
    if (OPENING_BRACKETS.has(character)) depth += 1;
    if (CLOSING_BRACKETS.has(character)) depth -= 1;
    if (character === ":" && depth === 0) {
      parts.push(current);
      current = "";
      continue;
    }
    current += character;
  }
  parts.push(current);
  return parts;
}

function getVariants(token) {
  return splitVariants(token).slice(0, -1);
}

function hasUtility(token) {
  return splitVariants(token).at(-1) !== "";
}

export function toTokens(text) {
  return text.split(/\s+/).filter(Boolean).filter(hasUtility);
}

export function groupKey(name, state) {
  return `group/${name}:${state}`;
}

function getVariantKey(variant) {
  if (REQUIRED_STATES.includes(variant)) return variant;
  const arbitrary = ARBITRARY_STATE_VARIANT.exec(variant);
  if (arbitrary) return arbitrary[1];
  const group = GROUP_STATE_VARIANT.exec(variant);
  return group ? groupKey(group[2] ?? "", group[1]) : undefined;
}

export function groupMarkerKey(name) {
  return `group-marker/${name}`;
}

export function getStateKeys(token) {
  const marker = GROUP_MARKER.exec(token);
  if (marker) return [groupMarkerKey(marker[1] ?? "")];
  return getVariants(token).map(getVariantKey).filter(Boolean);
}

export function getGroupNames(keys) {
  const prefix = groupMarkerKey("");
  return [...keys].filter((key) => key.startsWith(prefix)).map((key) => key.slice(prefix.length));
}

function isInvisibleHoverToken(token) {
  const isHover = getVariants(token).some((variant) => HOVER_VARIANT.test(variant));
  return isHover && INVISIBLE_HOVER_UTILITY.test(splitVariants(token).at(-1));
}

export function findInvisibleHoverTokens(tokens) {
  return [...new Set(tokens.filter(isInvisibleHoverToken))];
}
