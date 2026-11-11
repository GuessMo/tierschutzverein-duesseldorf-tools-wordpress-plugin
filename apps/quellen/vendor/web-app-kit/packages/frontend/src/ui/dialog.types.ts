import type { ComponentProps } from "react";
import type { Dialog as DialogPrimitive } from "radix-ui";

type ContentCloseButton =
  { showCloseButton?: true; closeLabel: string } | { showCloseButton: false; closeLabel?: never };

type FooterCloseButton =
  { showCloseButton?: false; closeLabel?: never } | { showCloseButton: true; closeLabel: string };

export type DialogContentProps = ComponentProps<typeof DialogPrimitive.Content> &
  ContentCloseButton;

export type DialogFooterProps = ComponentProps<"div"> & FooterCloseButton;
