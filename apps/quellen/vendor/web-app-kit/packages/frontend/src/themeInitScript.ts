import type { Theme } from "./ThemeProvider.js";

function toScriptLiteral(value: string): string {
  return JSON.stringify(value).replace(/</g, "\\u003c");
}

export function themeInitScript(storageKey: string, defaultTheme: Theme): string {
  const key = toScriptLiteral(storageKey);
  const fallback = toScriptLiteral(defaultTheme);
  return [
    "(function(){",
    `var theme=${fallback};`,
    `try{var stored=localStorage.getItem(${key});`,
    "if(stored==='light'||stored==='dark'||stored==='system'){theme=stored;}}",
    `catch(error){theme=${fallback};}`,
    "var resolved=theme==='system'",
    "?(window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'):theme;",
    "var root=document.documentElement;",
    "root.classList.remove('light','dark');",
    "root.classList.add(resolved);",
    "})();",
  ].join("");
}
