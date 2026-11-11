import { StrictMode } from "react";
import type { ReactNode } from "react";
import { createRoot } from "react-dom/client";
import { ThemeProvider } from "@web-app-kit/frontend";
import { CatalogApp } from "./catalog-app";
import { PreviewApp } from "./preview/preview-app";
import { preparePreviewDocument } from "./preview/prepare-preview-document";
import { PREVIEW_PREFIX } from "./routing/paths";
import { THEME_STORAGE_KEY } from "./shell/theme-storage-key";
import "./index.css";

function renderApp(): ReactNode {
  const { pathname, search } = window.location;
  if (!pathname.startsWith(PREVIEW_PREFIX)) {
    return (
      <ThemeProvider defaultTheme="system" storageKey={THEME_STORAGE_KEY}>
        <CatalogApp />
      </ThemeProvider>
    );
  }
  preparePreviewDocument(search);
  return <PreviewApp previewId={decodeURIComponent(pathname.slice(PREVIEW_PREFIX.length))} />;
}

class MissingRootError extends Error {
  constructor() {
    super("Element #root fehlt in index.html.");
    this.name = "MissingRootError";
  }
}

const root = document.getElementById("root");
if (!root) throw new MissingRootError();

createRoot(root).render(<StrictMode>{renderApp()}</StrictMode>);
