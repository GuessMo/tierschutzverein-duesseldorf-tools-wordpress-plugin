import type { AppVersionProps } from "./app-version.types.js";

export function AppVersion({ label, version }: AppVersionProps) {
  return (
    <p data-slot="app-version" className="flex items-baseline gap-1 tabular-nums">
      <span>{label}</span>
      <span className="text-foreground">{version}</span>
    </p>
  );
}
