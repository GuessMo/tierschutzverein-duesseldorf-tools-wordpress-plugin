import assert from "node:assert/strict";
import { test } from "node:test";
import { installDomEnvironment } from "./ui/dom-env.helper.js";

installDomEnvironment();

const { cleanup, render } = await import("@testing-library/react");
const { themeInitScript } = await import("./themeInitScript.js");
const { ThemeProvider } = await import("./ThemeProvider.js");

type Theme = "light" | "dark" | "system";

const STORAGE_KEY = "app-theme";

function stubMatchMedia(prefersDark: boolean) {
  Object.defineProperty(window, "matchMedia", {
    configurable: true,
    value: (query: string) => ({
      matches: prefersDark && query.includes("dark"),
      addEventListener: () => undefined,
      removeEventListener: () => undefined,
    }),
  });
}

function prepare(stored: string | null, prefersDark: boolean) {
  localStorage.clear();
  if (stored !== null) localStorage.setItem(STORAGE_KEY, stored);
  stubMatchMedia(prefersDark);
  document.documentElement.className = "light dark";
}

function classFromScript(stored: string | null, fallback: Theme, prefersDark: boolean) {
  prepare(stored, prefersDark);
  window.eval(themeInitScript(STORAGE_KEY, fallback));
  return document.documentElement.className;
}

function classFromProvider(stored: string | null, fallback: Theme, prefersDark: boolean) {
  prepare(stored, prefersDark);
  render(<ThemeProvider storageKey={STORAGE_KEY} defaultTheme={fallback}>x</ThemeProvider>);
  const resolved = document.documentElement.className;
  cleanup();
  return resolved;
}

const STORED_VALUES = [null, "light", "dark", "system", "bogus", ""];
const DEFAULTS: Theme[] = ["light", "dark", "system"];

test.afterEach(() => localStorage.clear());

test("init script and ThemeProvider resolve every case identically", () => {
  for (const stored of STORED_VALUES) {
    for (const fallback of DEFAULTS) {
      for (const prefersDark of [false, true]) {
        const fromScript = classFromScript(stored, fallback, prefersDark);
        const fromProvider = classFromProvider(stored, fallback, prefersDark);
        assert.equal(fromScript, fromProvider, JSON.stringify({ stored, fallback, prefersDark }));
        assert.match(fromScript, /^(light|dark)$/);
      }
    }
  }
});

test("stored theme wins, invalid values fall back to the default", () => {
  assert.equal(classFromScript("dark", "light", false), "dark");
  assert.equal(classFromScript("bogus", "light", true), "light");
  assert.equal(classFromScript("system", "light", true), "dark");
});

test("throwing localStorage falls back to the default in script and provider", () => {
  const original = Object.getOwnPropertyDescriptor(globalThis, "localStorage");
  const throwing = {
    getItem: () => {
      throw new Error("blocked");
    },
    setItem: () => {
      throw new Error("blocked");
    },
    clear: () => undefined,
  };
  Object.defineProperty(globalThis, "localStorage", { configurable: true, value: throwing });
  Object.defineProperty(window, "localStorage", { configurable: true, value: throwing });
  try {
    stubMatchMedia(true);
    window.eval(themeInitScript(STORAGE_KEY, "light"));
    assert.equal(document.documentElement.className, "light");
    render(<ThemeProvider storageKey={STORAGE_KEY} defaultTheme="dark">x</ThemeProvider>);
    assert.equal(document.documentElement.className, "dark");
    cleanup();
  } finally {
    if (original) Object.defineProperty(globalThis, "localStorage", original);
    if (original) Object.defineProperty(window, "localStorage", original);
  }
});

test("storage key cannot close the surrounding script tag", () => {
  const script = themeInitScript("</script><script>alert(1)", "system");
  assert.equal(script.includes("</script>"), false);
});
