import { Checkbox, Label } from "@web-app-kit/frontend/ui";
import type { CheckboxFieldProps } from "./common.types";

export function CheckboxField({ id, label, checked, hint, onCheckedChange }: CheckboxFieldProps) {
  const hintId = `${id}-hint`;
  return (
    <div className="flex items-start gap-2.5">
      <Checkbox
        id={id}
        checked={checked}
        aria-describedby={hint ? hintId : undefined}
        className="mt-0.5"
        onCheckedChange={(next) => onCheckedChange(next === true)}
      />
      <div className="flex flex-col gap-1">
        <Label htmlFor={id} className="font-normal">
          {label}
        </Label>
        {hint && (
          <p id={hintId} className="text-xs text-muted-foreground">
            {hint}
          </p>
        )}
      </div>
    </div>
  );
}
