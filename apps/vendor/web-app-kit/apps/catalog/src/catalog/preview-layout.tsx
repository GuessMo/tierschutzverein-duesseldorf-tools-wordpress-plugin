import type { ReactNode } from "react";

export function PreviewRow({ children }: { children: ReactNode }) {
  return <div className="flex flex-wrap items-center gap-3">{children}</div>;
}

export function PreviewStack({ children }: { children: ReactNode }) {
  return <div className="flex max-w-sm flex-col gap-4">{children}</div>;
}
