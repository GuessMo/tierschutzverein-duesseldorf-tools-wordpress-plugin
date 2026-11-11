import * as React from "react";
import { type VariantProps } from "class-variance-authority";
import { ToggleGroup as ToggleGroupPrimitive } from "radix-ui";

import { cn } from "./cn.js";
import { toggleVariants } from "./toggle.js";
import type { ToggleGroupProps, ToggleGroupStyle } from "./toggle-group.types.js";

const ToggleGroupContext = React.createContext<ToggleGroupStyle>({
  size: "default",
  variant: "default",
  spacing: 2,
  orientation: "horizontal",
});

const TOGGLE_GROUP_CLASSES = [
  "group/toggle-group flex w-fit flex-row items-center gap-[--spacing(var(--gap))]",
  "rounded-lg data-[size=sm]:rounded-[min(var(--radius-md),10px)]",
  "data-vertical:flex-col data-vertical:items-stretch",
];

const TOGGLE_GROUP_ITEM_CLASSES = [
  "shrink-0 focus:z-10 focus-visible:z-10",
  "group-data-[spacing=0]/toggle-group:rounded-none group-data-[spacing=0]/toggle-group:px-2",
  "group-data-[spacing=0]/toggle-group:has-data-[icon=inline-end]:pr-1.5",
  "group-data-[spacing=0]/toggle-group:has-data-[icon=inline-start]:pl-1.5",
  "group-data-horizontal/toggle-group:data-[spacing=0]:first:rounded-l-lg",
  "group-data-vertical/toggle-group:data-[spacing=0]:first:rounded-t-lg",
  "group-data-horizontal/toggle-group:data-[spacing=0]:last:rounded-r-lg",
  "group-data-vertical/toggle-group:data-[spacing=0]:last:rounded-b-lg",
  "group-data-horizontal/toggle-group:data-[spacing=0]:data-[variant=outline]:border-l-0",
  "group-data-vertical/toggle-group:data-[spacing=0]:data-[variant=outline]:border-t-0",
  "group-data-horizontal/toggle-group:data-[spacing=0]:data-[variant=outline]:first:border-l",
  "group-data-vertical/toggle-group:data-[spacing=0]:data-[variant=outline]:first:border-t",
];

function ToggleGroup({
  className,
  variant,
  size,
  spacing = 2,
  orientation = "horizontal",
  label,
  children,
  ...props
}: ToggleGroupProps) {
  return (
    <ToggleGroupPrimitive.Root
      data-slot="toggle-group"
      data-variant={variant}
      data-size={size}
      data-spacing={spacing}
      aria-label={label}
      orientation={orientation}
      style={{ "--gap": spacing } as React.CSSProperties}
      className={cn(TOGGLE_GROUP_CLASSES, className)}
      {...props}
    >
      <ToggleGroupContext.Provider value={{ variant, size, spacing, orientation }}>
        {children}
      </ToggleGroupContext.Provider>
    </ToggleGroupPrimitive.Root>
  );
}

function ToggleGroupItem({
  className,
  children,
  variant = "default",
  size = "default",
  ...props
}: React.ComponentProps<typeof ToggleGroupPrimitive.Item> & VariantProps<typeof toggleVariants>) {
  const context = React.useContext(ToggleGroupContext);
  const resolvedVariant = context.variant || variant;
  const resolvedSize = context.size || size;

  return (
    <ToggleGroupPrimitive.Item
      data-slot="toggle-group-item"
      data-variant={resolvedVariant}
      data-size={resolvedSize}
      data-spacing={context.spacing}
      className={cn(
        TOGGLE_GROUP_ITEM_CLASSES,
        toggleVariants({ variant: resolvedVariant, size: resolvedSize }),
        className,
      )}
      {...props}
    >
      {children}
    </ToggleGroupPrimitive.Item>
  );
}

export { ToggleGroup, ToggleGroupItem };
