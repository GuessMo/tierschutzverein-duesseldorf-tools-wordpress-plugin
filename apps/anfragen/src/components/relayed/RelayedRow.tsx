import {
  IconAlertTriangle,
  IconCircleCheck,
  IconCircleX,
  IconEdit,
  IconExternalLink,
} from "@tabler/icons-react";
import { Badge, Button } from "@web-app-kit/frontend/ui";
import type { RelayedStateLevel } from "@/types";
import type { RelayedRowProps } from "./relayed.types";

const ROW_CLASSES = [
  "flex flex-col gap-3 rounded-xl border p-4",
  "md:grid md:grid-cols-[12rem_minmax(0,1fr)_14rem] md:gap-6",
].join(" ");

const STATE_ICONS: Record<RelayedStateLevel, typeof IconCircleCheck> = {
  success: IconCircleCheck,
  warning: IconAlertTriangle,
  danger: IconCircleX,
};

const STATE_CLASSES: Record<RelayedStateLevel, string> = {
  success: "text-foreground",
  warning: "text-warning",
  danger: "text-destructive",
};

export function RelayedRow({ item }: RelayedRowProps) {
  const StateIcon = STATE_ICONS[item.stateLevel];
  return (
    <li className={ROW_CLASSES}>
      <div className="flex flex-col gap-1.5">
        <span className="text-xs text-muted-foreground">{item.createdLabel}</span>
        <span className="font-medium">{item.animal?.name ?? "Tier gelöscht"}</span>
        <Badge variant="secondary" className="self-start">
          {item.kindLabel}
        </Badge>
        <div className="flex flex-wrap gap-1">
          {item.animal?.toolUrl && (
            <Button asChild variant="ghost" size="icon-sm">
              <a
                href={item.animal.toolUrl}
                target="_blank"
                rel="noopener"
                aria-label="In Tierverwaltung öffnen"
                title="In Tierverwaltung öffnen"
              >
                <IconEdit aria-hidden />
              </a>
            </Button>
          )}
          {item.animal?.viewUrl && (
            <Button asChild variant="ghost" size="icon-sm">
              <a
                href={item.animal.viewUrl}
                target="_blank"
                rel="noreferrer"
                aria-label="Tierseite ansehen"
                title="Tierseite ansehen"
              >
                <IconExternalLink aria-hidden />
              </a>
            </Button>
          )}
        </div>
      </div>
      <div className="flex min-w-0 flex-col gap-1.5 text-sm">
        <span className="font-medium">{item.name}</span>
        <span className="text-muted-foreground">
          {[item.email, item.phone].filter(Boolean).join(" · ")}
        </span>
        <p className="whitespace-pre-line">{item.message || "Ohne Nachrichtentext"}</p>
      </div>
      <p className={`flex items-start gap-2 text-sm ${STATE_CLASSES[item.stateLevel]}`}>
        <StateIcon aria-hidden className="mt-0.5 size-4 shrink-0" />
        {item.stateLabel}
      </p>
    </li>
  );
}
