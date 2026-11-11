import type { ReactNode } from "react";

const TEXT_LINK_CLASSES = [
  "w-fit rounded-sm font-medium text-foreground underline underline-offset-4",
  "decoration-muted-foreground/60 hover:decoration-foreground active:translate-y-px",
  "focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-hidden",
].join(" ");

export function TextLink({ href, children }: { href: string; children: ReactNode }) {
  return (
    <a href={href} className={TEXT_LINK_CLASSES}>
      {children}
    </a>
  );
}
