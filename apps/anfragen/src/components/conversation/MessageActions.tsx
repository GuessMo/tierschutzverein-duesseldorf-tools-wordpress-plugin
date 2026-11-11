import { useState } from "react";
import { IconPencil, IconSend, IconTrash } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import { messagesApi } from "@/api";
import { runAction } from "@/lib";
import type { MessageBubbleProps } from "./conversation.types";
import { MessageEditor } from "./MessageEditor";
import { PendingCountdown } from "./PendingCountdown";

const DELETE_CONFIRMATION = {
  title: "Nachricht löschen?",
  description: "Die Nachricht wird entfernt und nicht versendet.",
  confirmLabel: "Löschen",
  isDestructive: true,
};

export function MessageActions({ conversationId, message, ask, onChanged }: MessageBubbleProps) {
  const [isEditing, setIsEditing] = useState(false);
  const remove = () =>
    ask(DELETE_CONFIRMATION, () =>
      runAction(() => messagesApi.remove(conversationId, message.id), onChanged),
    );
  const sendNow = () => runAction(() => messagesApi.sendNow(conversationId, message.id), onChanged);
  if (isEditing) {
    return (
      <MessageEditor
        initial={message.body}
        onCancel={() => setIsEditing(false)}
        onSave={(body) =>
          runAction(
            () => messagesApi.update(conversationId, message.id, body),
            async () => {
              setIsEditing(false);
              await onChanged();
            },
          )
        }
      />
    );
  }
  return (
    <div className="flex flex-wrap items-center gap-1">
      {message.isPending && <PendingCountdown seconds={message.remaining} onDone={onChanged} />}
      {message.isPending && (
        <Button size="xs" variant="outline" onClick={() => void sendNow()}>
          <IconSend data-icon="inline-start" />
          Jetzt senden
        </Button>
      )}
      <Button
        size="icon-xs"
        variant="ghost"
        aria-label="Nachricht bearbeiten"
        onClick={() => setIsEditing(true)}
      >
        <IconPencil aria-hidden />
      </Button>
      <Button size="icon-xs" variant="ghost" aria-label="Nachricht löschen" onClick={remove}>
        <IconTrash aria-hidden />
      </Button>
    </div>
  );
}
