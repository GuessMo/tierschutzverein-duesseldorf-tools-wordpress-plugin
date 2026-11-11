import * as React from "react";
import { Select as SelectPrimitive } from "radix-ui";
import { IconCheck, IconChevronDown, IconChevronUp } from "@tabler/icons-react";
import { cn } from "./cn.js";

function SelectLabel({ className, ...props }: React.ComponentProps<typeof SelectPrimitive.Label>) {
  return (
    <SelectPrimitive.Label
      data-slot="select-label"
      className={cn("px-1.5 py-1 text-xs text-muted-foreground", className)}
      {...props}
    />
  );
}

const SELECT_ITEM_CLASSES = [
  "focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset",
  "relative flex w-full cursor-default items-center gap-1.5 rounded-md py-1",
  "pr-8 pl-1.5 text-sm outline-hidden select-none focus:bg-primary-subtle",
  "focus:text-primary-subtle-foreground",
  "data-disabled:pointer-events-none data-disabled:opacity-50",
  "[&_svg]:pointer-events-none [&_svg]:shrink-0",
  "[&_svg:not([class*='size-'])]:size-4 *:[span]:last:flex",
  "*:[span]:last:items-center *:[span]:last:gap-2",
];

function SelectItem({
  className,
  children,
  ...props
}: React.ComponentProps<typeof SelectPrimitive.Item>) {
  return (
    <SelectPrimitive.Item
      data-slot="select-item"
      className={cn(SELECT_ITEM_CLASSES, className)}
      {...props}
    >
      <span
        className={cn([
          "pointer-events-none absolute right-2 flex size-4 items-center",
          "justify-center text-primary",
        ])}
      >
        <SelectPrimitive.ItemIndicator>
          <IconCheck className="pointer-events-none" />
        </SelectPrimitive.ItemIndicator>
      </span>
      <SelectPrimitive.ItemText>{children}</SelectPrimitive.ItemText>
    </SelectPrimitive.Item>
  );
}

function SelectSeparator({
  className,
  ...props
}: React.ComponentProps<typeof SelectPrimitive.Separator>) {
  return (
    <SelectPrimitive.Separator
      data-slot="select-separator"
      className={cn("pointer-events-none -mx-1 my-1 h-px bg-border", className)}
      {...props}
    />
  );
}

const SELECT_SCROLL_BUTTON_CLASSES = [
  "z-10 flex cursor-default items-center justify-center bg-popover py-1",
  "[&_svg:not([class*='size-'])]:size-4",
];

function SelectScrollUpButton({
  className,
  ...props
}: React.ComponentProps<typeof SelectPrimitive.ScrollUpButton>) {
  return (
    <SelectPrimitive.ScrollUpButton
      data-slot="select-scroll-up-button"
      className={cn(SELECT_SCROLL_BUTTON_CLASSES, className)}
      {...props}
    >
      <IconChevronUp />
    </SelectPrimitive.ScrollUpButton>
  );
}

function SelectScrollDownButton({
  className,
  ...props
}: React.ComponentProps<typeof SelectPrimitive.ScrollDownButton>) {
  return (
    <SelectPrimitive.ScrollDownButton
      data-slot="select-scroll-down-button"
      className={cn(SELECT_SCROLL_BUTTON_CLASSES, className)}
      {...props}
    >
      <IconChevronDown />
    </SelectPrimitive.ScrollDownButton>
  );
}

export { SelectLabel, SelectItem, SelectSeparator, SelectScrollUpButton, SelectScrollDownButton };
