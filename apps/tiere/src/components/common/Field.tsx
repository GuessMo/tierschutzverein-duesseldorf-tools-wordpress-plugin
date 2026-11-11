import { Label } from "@web-app-kit/frontend/ui";
import { HelpPopover } from "@/components/help";
import type { FieldProps } from "./common.types";

export function Field({ id, label, hint, children }: FieldProps) {
  return (
    <div className="flex flex-col gap-1.5">
      <div className="flex min-h-6 items-center gap-1">
        <Label htmlFor={id}>{label}</Label>
        <HelpPopover fieldId={id} label={label} />
      </div>
      {children}
      {hint && <p className="text-xs text-muted-foreground">{hint}</p>}
    </div>
  );
}
