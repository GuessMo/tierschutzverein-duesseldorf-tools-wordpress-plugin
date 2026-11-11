import { Label } from "@web-app-kit/frontend/ui";
import type { FieldRowProps } from "./controls.types";

export function FieldRow({ label, htmlFor, children }: FieldRowProps) {
  return (
    <div className="flex flex-col gap-2">
      <Label htmlFor={htmlFor} className="text-xs text-muted-foreground">
        {label}
      </Label>
      {children}
    </div>
  );
}
