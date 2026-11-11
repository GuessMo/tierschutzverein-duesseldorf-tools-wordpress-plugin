import type { ComponentProps } from "react";
import type { Progress as ProgressPrimitive } from "radix-ui";

type ProgressLabel =
  | { "aria-label": string; "aria-labelledby"?: never }
  | { "aria-label"?: never; "aria-labelledby": string };

export type ProgressProps = ComponentProps<typeof ProgressPrimitive.Root> & ProgressLabel;
