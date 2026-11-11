import { getStateKeys, toTokens } from "./state-tokens.js";

export function emptyResolution() {
  return { tokens: [], keys: new Set(), isKitCovered: false, isUnresolved: false };
}

export function unresolvedResolution() {
  return { ...emptyResolution(), isUnresolved: true };
}

export function kitResolution() {
  return { ...emptyResolution(), isKitCovered: true };
}

export function tokenResolution(tokens) {
  return { ...emptyResolution(), tokens, keys: new Set(tokens.flatMap(getStateKeys)) };
}

export function textResolution(text) {
  return tokenResolution(toTokens(text));
}

export function combineAll(resolutions) {
  return {
    tokens: resolutions.flatMap((resolution) => resolution.tokens),
    keys: new Set(resolutions.flatMap((resolution) => [...resolution.keys])),
    isKitCovered: resolutions.some((resolution) => resolution.isKitCovered),
    isUnresolved: resolutions.some((resolution) => resolution.isUnresolved),
  };
}

function intersectKeys(resolutions) {
  const [first, ...rest] = resolutions.map((resolution) => resolution.keys);
  return new Set([...first].filter((key) => rest.every((keys) => keys.has(key))));
}

export function combineAlternatives(resolutions) {
  return {
    tokens: resolutions.flatMap((resolution) => resolution.tokens),
    keys: intersectKeys(resolutions),
    isKitCovered: resolutions.every((resolution) => resolution.isKitCovered),
    isUnresolved: resolutions.some((resolution) => resolution.isUnresolved),
  };
}

export function asConditional(resolution) {
  return { ...emptyResolution(), tokens: resolution.tokens };
}
