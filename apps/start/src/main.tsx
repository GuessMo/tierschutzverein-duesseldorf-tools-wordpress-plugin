import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import "@/styles/index.css";
import { ThemeProvider } from "@web-app-kit/frontend";
import { DashboardScreen } from "@/components/dashboard";

const root = document.getElementById("root");
if (!root) throw new Error("Root-Element fehlt.");

createRoot(root).render(
  <StrictMode>
    <ThemeProvider defaultTheme="system" storageKey="tsvd-start-theme">
      <DashboardScreen />
    </ThemeProvider>
  </StrictMode>,
);
