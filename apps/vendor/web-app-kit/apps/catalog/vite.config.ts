import { readFileSync } from "node:fs";
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import { themeInitScript } from "../../packages/frontend/src/themeInitScript.ts";
import { THEME_STORAGE_KEY } from "./src/shell/theme-storage-key";

const frontendPackage = JSON.parse(
  readFileSync(new URL("../../packages/frontend/package.json", import.meta.url), "utf8"),
) as { version: string };

export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
    {
      name: "theme-init",
      transformIndexHtml: () => [
        {
          tag: "script",
          children: themeInitScript(THEME_STORAGE_KEY, "system"),
          injectTo: "head-prepend",
        },
      ],
    },
  ],
  define: {
    "import.meta.env.VITE_KIT_VERSION": JSON.stringify(frontendPackage.version),
  },
  server: { port: 5199, strictPort: true },
  preview: { port: 5199, strictPort: true },
});
