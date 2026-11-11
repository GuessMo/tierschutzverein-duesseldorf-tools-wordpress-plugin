import type * as React from "react";

type CardSize = "default" | "sm";

type StaticCardProps = {
  interactive?: false;
  asChild?: boolean;
};

type InteractiveCardProps = {
  interactive: true;
  asChild: true;
};

export type CardProps = React.ComponentProps<"div"> & {
  size?: CardSize;
} & (StaticCardProps | InteractiveCardProps);

export type CardSectionProps = React.ComponentProps<"div"> & { asChild?: boolean };
