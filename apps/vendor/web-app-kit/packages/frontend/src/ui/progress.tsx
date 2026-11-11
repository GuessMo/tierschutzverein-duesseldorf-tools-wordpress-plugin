import { Progress as ProgressPrimitive } from "radix-ui";

import { cn } from "./cn.js";
import type { ProgressProps } from "./progress.types.js";

const DEFAULT_MAX = 100;

const PROGRESS_CLASSES =
  "relative flex h-1 w-full items-center overflow-x-hidden rounded-full bg-muted";

const PROGRESS_INDICATOR_CLASSES = [
  "size-full flex-1 bg-primary transition-all",
  "motion-reduce:transition-none",
];

function toPercent(value: number | null | undefined, max: number): number {
  if (!value || max <= 0) return 0;
  return Math.min(100, Math.max(0, (value / max) * 100));
}

function Progress({ className, value, max = DEFAULT_MAX, ...props }: ProgressProps) {
  return (
    <ProgressPrimitive.Root
      data-slot="progress"
      className={cn(PROGRESS_CLASSES, className)}
      value={value}
      max={max}
      {...props}
    >
      <ProgressPrimitive.Indicator
        data-slot="progress-indicator"
        className={cn(PROGRESS_INDICATOR_CLASSES)}
        style={{ transform: `translateX(-${100 - toPercent(value, max)}%)` }}
      />
    </ProgressPrimitive.Root>
  );
}

export { Progress };
