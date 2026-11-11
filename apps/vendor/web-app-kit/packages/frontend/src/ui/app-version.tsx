import { KIT_VERSION } from "../kitVersion.js";
import type { AppVersionProps } from "./app-version.types.js";

export function AppVersion({ label, version, kitLabel }: AppVersionProps) {
  return (
    <p data-slot="app-version" className="flex items-baseline gap-1 tabular-nums">
      <span>{label}</span>
      <span className="text-foreground">{version}</span>
      {kitLabel && (
        <>
          <span aria-hidden="true" className="px-1">
            ·
          </span>
          <span>{kitLabel}</span>
          <span className="text-foreground">{KIT_VERSION}</span>
        </>
      )}
    </p>
  );
}
