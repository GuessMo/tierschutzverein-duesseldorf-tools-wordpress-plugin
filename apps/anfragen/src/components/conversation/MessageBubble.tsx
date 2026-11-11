import { cn } from "@web-app-kit/frontend/ui";
import type { MessageDirection } from "@/types";
import type { MessageBubbleProps } from "./conversation.types";
import { MessageActions } from "./MessageActions";

const BUBBLE_CLASSES: Record<MessageDirection, string> = {
  in: "self-start bg-muted",
  out: "self-end bg-primary-subtle",
  note: "self-stretch border border-dashed bg-background",
};

const DIRECTION_LABELS: Record<MessageDirection, string> = {
  in: "Nachricht der Person",
  out: "Nachricht vom Verein",
  note: "Interne Notiz",
};

export function MessageBubble(props: MessageBubbleProps) {
  const { message } = props;
  return (
    <li
      aria-label={DIRECTION_LABELS[message.direction]}
      className={cn(
        "flex max-w-[85%] flex-col gap-1.5 rounded-xl px-3.5 py-2.5",
        BUBBLE_CLASSES[message.direction],
      )}
    >
      <span className="text-xs text-muted-foreground">
        {message.author} · {message.stamp}
        {message.isEdited && " · bearbeitet"}
      </span>
      <p className="text-sm whitespace-pre-wrap">{message.body}</p>
      {(message.isPending || message.isMutable) && <MessageActions {...props} />}
    </li>
  );
}
