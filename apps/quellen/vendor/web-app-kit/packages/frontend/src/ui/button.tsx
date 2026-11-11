import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { Slot } from "radix-ui";

import { cn } from "./cn.js";
import { FILLED_FOCUS_CLASSES, PRIMARY_SUBTLE_STATE_CLASSES } from "./interaction-states.js";

const buttonVariants = cva(
  [
    "group/button inline-flex shrink-0 items-center justify-center rounded-lg",
    "border border-transparent bg-clip-padding text-sm font-medium",
    "whitespace-nowrap transition-all outline-none focus-visible:outline-hidden select-none",
    "focus-visible:border-ring focus-visible:ring-3",
    "focus-visible:ring-ring/50 not-disabled:active:not-aria-[haspopup]:translate-y-px",
    "not-disabled:active:aria-[haspopup]:opacity-80",
    "disabled:cursor-not-allowed disabled:opacity-50",
    "aria-invalid:border-destructive aria-invalid:ring-3",
    "aria-invalid:ring-destructive/20 dark:aria-invalid:border-destructive/50",
    "dark:aria-invalid:ring-destructive/40 [&_svg]:pointer-events-none",
    "[&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
  ],
  {
    variants: {
      variant: {
        default: [
          "bg-primary text-primary-foreground not-disabled:hover:bg-primary-hover",
          "not-disabled:active:bg-primary-active",
          FILLED_FOCUS_CLASSES,
        ],
        outline: ["border-input bg-transparent", PRIMARY_SUBTLE_STATE_CLASSES],
        secondary: [
          "bg-secondary text-secondary-foreground",
          "not-disabled:hover:bg-secondary-hover not-disabled:active:bg-secondary-active",
          "aria-expanded:bg-secondary aria-expanded:text-secondary-foreground",
        ],
        tertiary: [
          "bg-tertiary text-tertiary-foreground not-disabled:hover:bg-tertiary-hover",
          "not-disabled:active:bg-tertiary-active",
          "not-disabled:active:text-tertiary-active-foreground",
          "aria-expanded:bg-tertiary aria-expanded:text-tertiary-foreground",
        ],
        ghost: PRIMARY_SUBTLE_STATE_CLASSES,
        destructive: [
          "bg-destructive/10 text-destructive not-disabled:hover:bg-destructive/20",
          "dark:bg-destructive/20 dark:not-disabled:hover:bg-destructive/26",
        ],
        link: [
          "text-primary underline-offset-4 not-disabled:hover:text-primary-hover",
          "not-disabled:hover:underline not-disabled:active:text-primary-active",
        ],
      },
      size: {
        default:
          "h-8 gap-1.5 px-2.5 has-data-[icon=inline-end]:pr-2 has-data-[icon=inline-start]:pl-2",
        xs: [
          "h-6 gap-1 rounded-[min(var(--radius-md),10px)] px-2 text-xs",
          "in-data-[slot=button-group]:rounded-lg has-data-[icon=inline-end]:pr-1.5",
          "has-data-[icon=inline-start]:pl-1.5 [&_svg:not([class*='size-'])]:size-3",
        ],
        sm: [
          "h-7 gap-1 rounded-[min(var(--radius-md),12px)] px-2.5 text-[0.8rem]",
          "in-data-[slot=button-group]:rounded-lg has-data-[icon=inline-end]:pr-1.5",
          "has-data-[icon=inline-start]:pl-1.5",
          "[&_svg:not([class*='size-'])]:size-3.5",
        ],
        lg: "h-9 gap-1.5 px-2.5 has-data-[icon=inline-end]:pr-2 has-data-[icon=inline-start]:pl-2",
        icon: "size-8",
        "icon-xs": [
          "size-6 rounded-[min(var(--radius-md),10px)]",
          "in-data-[slot=button-group]:rounded-lg",
          "[&_svg:not([class*='size-'])]:size-3",
        ],
        "icon-sm":
          "size-7 rounded-[min(var(--radius-md),12px)] in-data-[slot=button-group]:rounded-lg",
        "icon-lg": "size-9",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
);

function Button({
  className,
  variant = "default",
  size = "default",
  asChild = false,
  ...props
}: React.ComponentProps<"button"> &
  VariantProps<typeof buttonVariants> & {
    asChild?: boolean;
  }) {
  const Comp = asChild ? Slot.Root : "button";

  return (
    <Comp
      data-slot="button"
      data-variant={variant}
      data-size={size}
      className={cn(buttonVariants({ variant, size, className }))}
      {...props}
    />
  );
}

export { Button, buttonVariants };
