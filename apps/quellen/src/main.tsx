import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import "@/styles/index.css";
import { ThemeProvider } from "@web-app-kit/frontend";
import { App } from "./App";

const root = document.getElementById("root");
if (!root) throw new Error("Root-Element fehlt.");

createRoot(root).render(
  <StrictMode>
    <ThemeProvider defaultTheme="system" storageKey="tsvd-quellen-theme">
      <App />
    </ThemeProvider>
  </StrictMode>,
);
