import { useLayoutEffect, useRef, useState } from "react";
import { cn } from "@web-app-kit/frontend/ui";
import type { ScaleItem } from "./basics.types";

export function ScaleTile({
  name,
  className,
  property,
}: ScaleItem & {
  property: "backgroundColor" | "borderTopLeftRadius";
}) {
  const ref = useRef<HTMLDivElement>(null);
  const [value, setValue] = useState("");
  useLayoutEffect(() => {
    if (ref.current) setValue(getComputedStyle(ref.current)[property]);
  }, [property]);
  return (
    <div className="flex min-w-0 flex-col gap-2 text-xs">
      <div ref={ref} className={cn("h-14 border border-border", className)} />
      <span className="font-medium">{name}</span>
      <span className="truncate font-mono text-muted-foreground" title={value}>
        {value}
      </span>
    </div>
  );
}
