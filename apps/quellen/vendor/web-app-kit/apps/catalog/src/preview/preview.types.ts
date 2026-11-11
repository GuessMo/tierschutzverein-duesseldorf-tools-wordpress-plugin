import type { ResolvedTheme } from "@web-app-kit/frontend";

export interface PreviewFrameProps {
  previewId: string;
  theme: ResolvedTheme;
  title: string;
  minHeight?: string;
}

export type PreviewPairProps = Omit<PreviewFrameProps, "theme">;
