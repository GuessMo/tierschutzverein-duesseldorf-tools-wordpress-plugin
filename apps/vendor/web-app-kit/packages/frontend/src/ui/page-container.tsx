import { cn } from "./cn.js";
import type { PageContainerProps, PageContainerSize } from "./page-container.types.js";

const SIZE_CLASSES: Record<PageContainerSize, string> = {
  md: "max-w-3xl",
  lg: "max-w-7xl",
  full: "max-w-none",
};

export function PageContainer({ size = "lg", className, ...props }: PageContainerProps) {
  return (
    <div
      data-slot="page-container"
      data-size={size}
      className={cn("mx-auto w-full px-4 py-8 sm:px-6", SIZE_CLASSES[size], className)}
      {...props}
    />
  );
}
