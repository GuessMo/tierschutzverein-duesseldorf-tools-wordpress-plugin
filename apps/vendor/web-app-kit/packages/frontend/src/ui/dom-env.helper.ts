import { JSDOM } from "jsdom";

const FORCED_GLOBALS = new Set(["Event", "CustomEvent", "EventTarget", "navigator"]);

export function installDomEnvironment() {
  const dom = new JSDOM("<!doctype html><html><body></body></html>", {
    pretendToBeVisual: true,
    url: "http://localhost/",
  });
  for (const key of Object.getOwnPropertyNames(dom.window)) {
    if (key in globalThis && !FORCED_GLOBALS.has(key)) continue;
    Object.defineProperty(globalThis, key, {
      value: Reflect.get(dom.window, key),
      configurable: true,
    });
  }
  Object.assign(globalThis, { IS_REACT_ACT_ENVIRONMENT: true });
}
