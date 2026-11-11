import * as React from "react";
import { Switch as SwitchPrimitive } from "radix-ui";

import { cn } from "./cn.js";
import { FILLED_FOCUS_CLASSES } from "./interaction-states.js";

const SWITCH_BASE_CLASSES = [
  "peer group/switch relative inline-flex shrink-0 items-center rounded-full border",
  "border-transparent transition-all outline-none",
  "group-has-[:focus-visible]/field-label:border-transparent",
  "group-has-[:focus-visible]/field-label:ring-0 after:absolute after:-inset-x-3",
  "after:-inset-y-2 aria-invalid:border-destructive aria-invalid:ring-3",
  "aria-invalid:ring-destructive/20 data-[size=default]:h-[18.4px] data-[size=default]:w-[32px]",
  "data-[size=sm]:h-[14px] data-[size=sm]:w-[24px] dark:aria-invalid:border-destructive/50",
  "dark:aria-invalid:ring-destructive/40 data-checked:bg-primary data-unchecked:bg-input",
  "dark:data-unchecked:bg-input/80 data-disabled:cursor-not-allowed data-disabled:opacity-50",
];

const SWITCH_STATE_CLASSES = [
  FILLED_FOCUS_CLASSES,
  "dark:data-unchecked:bg-input",
  "data-unchecked:not-disabled:hover:border-primary",
  "data-unchecked:not-disabled:active:border-primary",
  "data-unchecked:not-disabled:active:inset-ring-1",
  "data-unchecked:not-disabled:active:inset-ring-primary",
  "data-checked:not-disabled:hover:bg-primary-hover",
  "data-checked:not-disabled:active:bg-primary-active",
];

const SWITCH_THUMB_CLASSES = [
  "pointer-events-none block rounded-full bg-background ring-0 transition-transform",
  "group-data-[size=default]/switch:size-4 group-data-[size=sm]/switch:size-3",
  "group-data-[size=default]/switch:data-checked:translate-x-[calc(100%-2px)]",
  "group-data-[size=sm]/switch:data-checked:translate-x-[calc(100%-2px)]",
  "dark:data-checked:bg-primary-foreground",
  "group-data-[size=default]/switch:data-unchecked:translate-x-0",
  "group-data-[size=sm]/switch:data-unchecked:translate-x-0 dark:data-unchecked:bg-foreground",
];

function Switch({
  className,
  size = "default",
  ...props
}: React.ComponentProps<typeof SwitchPrimitive.Root> & {
  size?: "sm" | "default";
}) {
  return (
    <SwitchPrimitive.Root
      data-slot="switch"
      data-size={size}
      className={cn(SWITCH_BASE_CLASSES, SWITCH_STATE_CLASSES, className)}
      {...props}
    >
      <SwitchPrimitive.Thumb data-slot="switch-thumb" className={cn(SWITCH_THUMB_CLASSES)} />
    </SwitchPrimitive.Root>
  );
}

export { Switch };
