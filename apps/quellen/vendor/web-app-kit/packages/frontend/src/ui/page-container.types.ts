import type { ComponentProps } from "react";

export type PageContainerSize = "md" | "lg" | "full";

export type PageContainerProps = ComponentProps<"div"> & { size?: PageContainerSize };
