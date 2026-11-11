import type { ToolControlsProps } from "@tsvd/tool-controls";

export type ToolBootstrap = ToolControlsProps;

declare global {
  interface Window {
    tsvdToolBootstrap?: ToolBootstrap;
  }
}
