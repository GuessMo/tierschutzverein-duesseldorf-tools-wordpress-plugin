import * as React from "react";

import { cn } from "./cn.js";

const SKELETON_CLASSES = [
  "animate-pulse rounded-md bg-accent",
  "motion-reduce:animate-none",
];

function Skeleton({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="skeleton"
      aria-hidden="true"
      className={cn(SKELETON_CLASSES, className)}
      {...props}
    />
  );
}

export { Skeleton };
