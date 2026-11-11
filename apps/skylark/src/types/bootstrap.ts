import type { ToolControlsProps } from "@tsvd/tool-controls";

export interface ToolBootstrap extends ToolControlsProps {
  restUrl: string;
  restRoot: string;
  nonce: string;
  adminUrl: string;
  toolUrl: string;
}

declare global {
  interface Window {
    tsvdToolBootstrap?: ToolBootstrap;
  }
}
