import type { ComponentProps } from "react";
import type { VariantProps } from "class-variance-authority";
import type { ToggleGroup as ToggleGroupPrimitive } from "radix-ui";
import type { toggleVariants } from "./toggle.js";

export type ToggleGroupOrientation = "horizontal" | "vertical";

export type ToggleGroupStyle = VariantProps<typeof toggleVariants> & {
  spacing?: number;
  orientation?: ToggleGroupOrientation;
};

type ToggleGroupLabel =
  | { label: string; "aria-labelledby"?: never }
  | { label?: never; "aria-labelledby": string };

export type ToggleGroupProps = ComponentProps<typeof ToggleGroupPrimitive.Root> &
  ToggleGroupStyle &
  ToggleGroupLabel;
