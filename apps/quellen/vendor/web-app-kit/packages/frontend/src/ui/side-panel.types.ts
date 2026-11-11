import type { ComponentProps, ReactNode, RefObject } from "react";

export type SidePanelSide = "left" | "right";

export type SidePanelWidth = "sm" | "md" | "lg";

type SidePanelCloseButton =
  | { onOpenChange?: never; closeLabel?: never; returnFocusRef?: never }
  | {
      onOpenChange: (open: boolean) => void;
      closeLabel: string;
      returnFocusRef?: RefObject<HTMLElement | null>;
    };

export type SidePanelProps = Omit<ComponentProps<"div">, "title"> & {
  side: SidePanelSide;
  width?: SidePanelWidth;
  title?: ReactNode;
  actions?: ReactNode;
  open?: boolean;
} & SidePanelCloseButton;

export interface SidePanelHeaderProps {
  title?: ReactNode;
  headingId: string;
  actions?: ReactNode;
  onClose?: () => void;
  closeLabel?: string;
}

export type SidePanelFrameProps = Omit<SidePanelProps, "open">;

export type SidePanelCloseButtonProps = Required<Pick<SidePanelHeaderProps, "onClose">> &
  Pick<SidePanelHeaderProps, "closeLabel">;
