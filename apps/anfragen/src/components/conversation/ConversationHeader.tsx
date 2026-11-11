import { Badge, Button } from "@web-app-kit/frontend/ui";
import { HelpPopover } from "@/components/common";
import { HELP } from "@/config/help";
import { STATUS_LABELS } from "@/config/statuses";
import { ActionsMenu } from "./ActionsMenu";
import { AnimalCard } from "./AnimalCard";
import { AssigneeSelect } from "./AssigneeSelect";
import { ContactLine } from "./ContactLine";
import type { ConversationHeaderProps } from "./conversation.types";

export function ConversationHeader(props: ConversationHeaderProps) {
  const { conversation, assignees, onAction, onAssign } = props;
  const status = conversation.isTrashed ? "Papierkorb" : STATUS_LABELS[conversation.calcStatus];
  return (
    <header className="flex flex-col gap-3 border-b pb-4">
      <div className="flex flex-wrap items-start justify-between gap-3">
        <div className="flex min-w-0 flex-col gap-1">
          <div className="flex flex-wrap items-center gap-2">
            <h1 className="text-xl font-semibold">{conversation.name || "Ohne Namen"}</h1>
            {status && <Badge variant="secondary">{status}</Badge>}
            <HelpPopover text={HELP.contact} />
          </div>
          <div className="flex flex-wrap gap-x-4 gap-y-1">
            {conversation.email && (
              <ContactLine
                label="E-Mail"
                value={conversation.email}
                href={`mailto:${conversation.email}`}
              />
            )}
            {conversation.phone && (
              <ContactLine
                label="Telefon"
                value={conversation.phone}
                href={`tel:${conversation.phone}`}
              />
            )}
          </div>
          <span className="text-xs text-muted-foreground">{conversation.createdLabel}</span>
        </div>
        <div className="flex items-center gap-2">
          <AssigneeSelect
            value={conversation.assignedUserId}
            assignees={assignees}
            onChange={onAssign}
          />
          <ActionsMenu conversation={conversation} onAction={onAction} />
        </div>
      </div>
      {conversation.pendingApproval && (
        <Button className="self-start" onClick={() => onAction("approve-owner")}>
          Halterin oder Halter freigeben
        </Button>
      )}
      {conversation.animal && <AnimalCard animal={conversation.animal} />}
    </header>
  );
}
