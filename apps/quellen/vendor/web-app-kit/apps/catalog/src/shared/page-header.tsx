import type { ReactNode } from "react";

export function PageHeader({
  title,
  aside,
  children,
}: {
  title: string;
  aside?: ReactNode;
  children: ReactNode;
}) {
  return (
    <header className="flex flex-col gap-2">
      <div className="flex flex-wrap items-center gap-3">
        <h1 className="text-2xl font-semibold tracking-tight text-balance">{title}</h1>
        {aside}
      </div>
      <p className="max-w-prose text-muted-foreground">{children}</p>
    </header>
  );
}
