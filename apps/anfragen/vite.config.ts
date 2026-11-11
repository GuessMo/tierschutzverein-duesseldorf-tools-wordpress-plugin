import { defineConfig, type Plugin } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import { fileURLToPath, URL } from "node:url";
import { themeInitScript } from "./node_modules/@web-app-kit/frontend/src/themeInitScript.ts";

const THEME_STORAGE_KEY = "tsvd-anfragen-theme";

function themeInitAsset(): Plugin {
  return {
    name: "theme-init-asset",
    generateBundle() {
      this.emitFile({
        type: "asset",
        fileName: "theme-init.js",
        source: themeInitScript(THEME_STORAGE_KEY, "system"),
      });
    },
  };
}

export default defineConfig({
  base: "./",
  plugins: [react(), tailwindcss(), themeInitAsset()],
  resolve: {
    alias: { "@": fileURLToPath(new URL("./src", import.meta.url)) },
  },
  build: {
    manifest: true,
    rollupOptions: { input: "src/main.tsx" },
  },
});
