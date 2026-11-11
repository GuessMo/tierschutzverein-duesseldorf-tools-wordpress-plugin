import type { ResolvedTheme } from "@web-app-kit/frontend";
import { PreviewFrame } from "./preview-frame";
import type { PreviewPairProps } from "./preview.types";

const THEMES: { theme: ResolvedTheme; label: string }[] = [
  { theme: "light", label: "Hell" },
  { theme: "dark", label: "Dunkel" },
];

export function PreviewPair({ previewId, title, minHeight }: PreviewPairProps) {
  return (
    <div className="grid gap-4 xl:grid-cols-2">
      {THEMES.map(({ theme, label }) => (
        <figure key={theme} className="flex min-w-0 flex-col gap-2">
          <figcaption className="text-xs font-medium text-muted-foreground">{label}</figcaption>
          <PreviewFrame
            previewId={previewId}
            theme={theme}
            title={`${title} (${label})`}
            minHeight={minHeight}
          />
        </figure>
      ))}
    </div>
  );
}
