import type { ButtonHTMLAttributes } from "react";
import { cn } from "@/lib/utils";

export function TileButton({ className, type, ...props }: ButtonHTMLAttributes<HTMLButtonElement>) {
  return (
    <button
      type={type ?? "button"}
      className={cn(
        "flex min-h-14 flex-col items-center justify-center gap-1",
        "rounded-lg border bg-background p-2 text-xs font-medium",
        "transition-colors outline-none hover:bg-surface-hover",
        "active:bg-accent focus-visible:ring-3",
        "focus-visible:ring-ring/50",
        "disabled:pointer-events-none disabled:opacity-50 [&_svg]:size-4",
        className,
      )}
      {...props}
    />
  );
}
