import type { ReactNode } from "react";

interface NoticeProps {
  icon: ReactNode;
  title: string;
  children: ReactNode;
  role?: "alert" | "status";
}

const NOTICE_CLASSES = [
  "grid grid-cols-[auto_1fr] gap-x-2 gap-y-1 rounded-lg border border-destructive/40",
  "bg-destructive/5 p-3 text-sm text-destructive [&_svg]:mt-0.5 [&_svg]:size-4",
].join(" ");

export function Notice({ icon, title, children, role = "alert" }: NoticeProps) {
  return (
    <div role={role} className={NOTICE_CLASSES}>
      {icon}
      <p className="font-medium">{title}</p>
      <p className="col-start-2 text-foreground">{children}</p>
    </div>
  );
}
