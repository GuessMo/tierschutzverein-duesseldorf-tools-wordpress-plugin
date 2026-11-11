import * as React from "react";
import { Checkbox as CheckboxPrimitive } from "radix-ui";
import { IconCheck, IconMinus } from "@tabler/icons-react";
import { cn } from "./cn.js";

const CHECKBOX_BASE_CLASSES = [
  "group/checkbox peer relative flex size-4 shrink-0 items-center justify-center",
  "rounded-[4px] border border-input transition-colors outline-none",
  "group-has-disabled/field:opacity-50 after:absolute after:-inset-x-3 after:-inset-y-2",
  "focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solid",
  "focus-visible:outline-ring disabled:cursor-not-allowed disabled:opacity-50",
  "aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20",
  "aria-invalid:aria-checked:border-primary dark:bg-input/30",
  "dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40",
];

const CHECKBOX_UNCHECKED_CLASSES = [
  "data-unchecked:not-disabled:hover:bg-primary-subtle",
  "data-unchecked:not-disabled:hover:border-primary",
  "data-unchecked:not-disabled:active:bg-primary-subtle",
  "data-unchecked:not-disabled:active:inset-ring-1",
  "data-unchecked:not-disabled:active:inset-ring-primary",
];

const CHECKBOX_CHECKED_CLASSES = [
  "data-checked:border-primary data-checked:bg-primary data-checked:text-primary-foreground",
  "dark:data-checked:bg-primary",
  "data-checked:not-disabled:hover:border-primary-hover",
  "data-checked:not-disabled:hover:bg-primary-hover",
  "data-checked:not-disabled:active:bg-primary-active",
  "data-[state=indeterminate]:border-primary data-[state=indeterminate]:bg-primary",
  "data-[state=indeterminate]:text-primary-foreground",
  "dark:data-[state=indeterminate]:bg-primary",
  "data-[state=indeterminate]:not-disabled:hover:bg-primary-hover",
  "data-[state=indeterminate]:not-disabled:active:bg-primary-active",
];

const CHECKBOX_INDICATOR_CLASSES =
  "grid place-content-center text-current transition-none [&>svg]:size-3.5";

function Checkbox({ className, ...props }: React.ComponentProps<typeof CheckboxPrimitive.Root>) {
  return (
    <CheckboxPrimitive.Root
      data-slot="checkbox"
      className={cn(
        CHECKBOX_BASE_CLASSES,
        CHECKBOX_UNCHECKED_CLASSES,
        CHECKBOX_CHECKED_CLASSES,
        className,
      )}
      {...props}
    >
      <CheckboxPrimitive.Indicator
        data-slot="checkbox-indicator"
        className={CHECKBOX_INDICATOR_CLASSES}
      >
        <IconCheck aria-hidden className="group-data-[state=indeterminate]/checkbox:hidden" />
        <IconMinus aria-hidden className="hidden group-data-[state=indeterminate]/checkbox:block" />
      </CheckboxPrimitive.Indicator>
    </CheckboxPrimitive.Root>
  );
}

export { Checkbox };
