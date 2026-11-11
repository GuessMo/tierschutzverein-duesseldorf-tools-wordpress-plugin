import { TONES } from "@/config/tones";
import { cn } from "@/lib/utils";
import type { ToneSwatchesProps } from "./controls.types";

const TRANSPARENT_PATTERN = "swatch-transparent";

export function ToneSwatches({
  label,
  value,
  onChange,
  allowTransparent = true,
}: ToneSwatchesProps) {
  const tones = allowTransparent ? TONES : TONES.filter((tone) => tone.id !== "transparent");
  return (
    <div className="flex flex-col gap-2">
      <span className="text-xs font-medium text-muted-foreground">{label}</span>
      <div className="flex flex-wrap gap-2" role="radiogroup" aria-label={label}>
        {tones.map((tone) => (
          <button
            key={tone.id}
            type="button"
            role="radio"
            title={tone.label}
            aria-label={tone.label}
            aria-checked={tone.id === value}
            onClick={() => onChange(tone.id)}
            style={{ background: tone.id === "transparent" ? undefined : tone.background }}
            className={cn(
              "size-7 rounded-full border border-input transition-transform outline-none",
              "hover:scale-110 active:scale-95 focus-visible:ring-3 focus-visible:ring-ring/50",
              "aria-checked:ring-2 aria-checked:ring-foreground",
              "aria-checked:ring-offset-2",
              "aria-checked:ring-offset-background",
              tone.id === "transparent" && TRANSPARENT_PATTERN,
            )}
          />
        ))}
      </div>
    </div>
  );
}
