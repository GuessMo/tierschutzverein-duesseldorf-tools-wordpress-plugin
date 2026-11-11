import { IconMail } from "@tabler/icons-react";
import { cn, INTERACTIVE_SURFACE_CLASSES } from "@web-app-kit/frontend/ui";
import { LIST_ROW_CLASSES } from "@tsvd/tool-controls";
import { issueHash } from "@/hooks";
import { formatDateTime } from "@/lib";
import type { IssueSummary } from "@/types";
import { IssueStatus } from "./IssueStatus";

const STRETCHED_TARGET_CLASSES = [
  "truncate text-left font-medium outline-none after:absolute after:inset-0 after:rounded-md",
  "hover:underline active:text-primary focus-visible:after:ring-2 focus-visible:after:ring-ring",
].join(" ");

const ICON_CLASSES = [
  "flex size-10 items-center justify-center rounded-md",
  "bg-primary-subtle text-primary-subtle-foreground",
].join(" ");

const ROW_CLASSES = [
  LIST_ROW_CLASSES,
  "grid-cols-[auto_minmax(0,1fr)_auto]",
  "md:grid-cols-[auto_minmax(0,3fr)_10rem_auto]",
].join(" ");

function blockLabel(count: number): string {
  return count === 1 ? "1 Block" : `${count} Blöcke`;
}

export function IssueRow({ issue }: { issue: IssueSummary }) {
  return (
    <li className={cn(INTERACTIVE_SURFACE_CLASSES, ROW_CLASSES)}>
      <span className={ICON_CLASSES}>
        <IconMail aria-hidden className="size-5" stroke={1.5} />
      </span>
      <span className="flex min-w-0 flex-col">
        <a href={issueHash(issue.id)} className={STRETCHED_TARGET_CLASSES}>
          {issue.title || "Ohne Betreff"}
        </a>
        <span className="truncate text-xs text-muted-foreground">
          {blockLabel(issue.blockCount)} · zuletzt geändert {formatDateTime(issue.modified)}
        </span>
      </span>
      <span className="hidden text-sm text-muted-foreground md:block">
        {blockLabel(issue.blockCount)}
      </span>
      <IssueStatus sentAt={issue.sentAt} />
    </li>
  );
}
