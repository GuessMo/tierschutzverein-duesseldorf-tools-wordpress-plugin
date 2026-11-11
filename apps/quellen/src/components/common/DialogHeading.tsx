import { DialogDescription, DialogHeader, DialogTitle } from "@web-app-kit/frontend/ui";
import type { DialogHeadingProps } from "./common.types";

export function DialogHeading({ title, description }: DialogHeadingProps) {
  return (
    <DialogHeader>
      <DialogTitle>{title}</DialogTitle>
      {description && <DialogDescription>{description}</DialogDescription>}
    </DialogHeader>
  );
}
