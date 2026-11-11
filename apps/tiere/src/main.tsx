import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import "@/styles/index.css";
import { ThemeProvider } from "@web-app-kit/frontend";
import { Toaster } from "@web-app-kit/frontend/ui";
import { App } from "./App";

const root = document.getElementById("root");
if (!root) throw new Error("Root-Element fehlt.");

createRoot(root).render(
  <StrictMode>
    <ThemeProvider defaultTheme="system" storageKey="tsvd-tiere-theme">
      <App />
      <Toaster closeLabel="Meldung schließen" regionLabel="Meldungen" />
    </ThemeProvider>
  </StrictMode>,
);
