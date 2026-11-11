import { Checkbox, Label } from "@web-app-kit/frontend/ui";
import { useId } from "react";
import type { CheckRowProps } from "./inspector.types";

export function CheckRow({ label, checked, onChange }: CheckRowProps) {
  const id = useId();
  return (
    <div className="flex items-center gap-2">
      <Checkbox id={id} checked={checked} onCheckedChange={(next) => onChange(next === true)} />
      <Label htmlFor={id} className="text-sm font-normal">
        {label}
      </Label>
    </div>
  );
}
