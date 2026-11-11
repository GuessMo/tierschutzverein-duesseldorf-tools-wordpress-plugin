import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { Toggle as TogglePrimitive } from "radix-ui";

import { cn } from "./cn.js";
import { TOGGLE_ON_FOCUS_CLASSES } from "./interaction-states.js";

const toggleVariants = cva(
  [
    "group/toggle inline-flex shrink-0 items-center justify-center gap-1 rounded-lg",
    "text-sm font-medium whitespace-nowrap transition-all outline-none select-none",
    "focus-visible:outline-hidden focus-visible:border-ring focus-visible:ring-3",
    "focus-visible:ring-ring/50 not-disabled:active:translate-y-px",
    "not-disabled:hover:bg-primary-subtle not-disabled:hover:text-primary-subtle-foreground",
    "data-[state=off]:not-disabled:active:border-primary",
    "data-[state=off]:not-disabled:active:bg-primary-subtle",
    "data-[state=off]:not-disabled:active:inset-ring-1",
    "data-[state=off]:not-disabled:active:inset-ring-primary",
    "data-[state=on]:bg-primary data-[state=on]:text-primary-foreground",
    "data-[state=on]:**:text-primary-foreground",
    "data-[state=on]:not-disabled:hover:bg-primary-hover",
    "data-[state=on]:not-disabled:hover:text-primary-foreground",
    "data-[state=on]:not-disabled:active:bg-primary-active",
    TOGGLE_ON_FOCUS_CLASSES,
    "disabled:cursor-not-allowed disabled:opacity-50",
    "aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20",
    "dark:aria-invalid:ring-destructive/40 [&_svg]:pointer-events-none [&_svg]:shrink-0",
    "[&_svg:not([class*='size-'])]:size-4",
  ],
  {
    variants: {
      variant: {
        default: "border border-transparent bg-transparent",
        outline: "border border-input bg-transparent data-[state=on]:border-primary",
      },
      size: {
        default:
          "h-8 min-w-8 px-2.5 has-data-[icon=inline-end]:pr-2 has-data-[icon=inline-start]:pl-2",
        xs: [
          "h-6 min-w-6 rounded-[min(var(--radius-md),10px)] px-2 text-xs",
          "[&_svg:not([class*='size-'])]:size-3",
        ],
        sm: [
          "h-7 min-w-7 rounded-[min(var(--radius-md),12px)] px-2.5 text-[0.8rem]",
          "has-data-[icon=inline-end]:pr-1.5 has-data-[icon=inline-start]:pl-1.5",
          "[&_svg:not([class*='size-'])]:size-3.5",
        ],
        lg: "h-9 min-w-9 px-2.5 has-data-[icon=inline-end]:pr-2 has-data-[icon=inline-start]:pl-2",
        icon: "size-8",
        "icon-xs": [
          "size-6 rounded-[min(var(--radius-md),10px)]",
          "[&_svg:not([class*='size-'])]:size-3",
        ],
        "icon-sm": "size-7 rounded-[min(var(--radius-md),12px)]",
        "icon-lg": "size-9",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
);

function Toggle({
  className,
  variant = "default",
  size = "default",
  ...props
}: React.ComponentProps<typeof TogglePrimitive.Root> & VariantProps<typeof toggleVariants>) {
  return (
    <TogglePrimitive.Root
      data-slot="toggle"
      data-variant={variant}
      data-size={size}
      className={cn(toggleVariants({ variant, size, className }))}
      {...props}
    />
  );
}

export { Toggle, toggleVariants };
