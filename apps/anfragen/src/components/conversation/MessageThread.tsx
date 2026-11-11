import { HelpPopover } from "@/components/common";
import { HELP } from "@/config/help";
import type { AskConfirmation, Conversation } from "@/types";
import { MessageBubble } from "./MessageBubble";

interface MessageThreadProps {
  conversation: Conversation;
  ask: AskConfirmation;
  onChanged: () => Promise<void>;
}

function FormFacts({ conversation }: { conversation: Conversation }) {
  if (conversation.facts.length === 0 && !conversation.interest) return null;
  return (
    <section aria-label="Formularangaben" className="rounded-xl border bg-card p-4 text-sm">
      {conversation.interest && <p className="mb-3 whitespace-pre-wrap">{conversation.interest}</p>}
      <dl className="grid gap-x-4 gap-y-1 sm:grid-cols-[auto_1fr]">
        {conversation.facts.map((fact) => (
          <div key={fact.label} className="contents">
            <dt className="text-muted-foreground">{fact.label}</dt>
            <dd>{fact.value}</dd>
          </div>
        ))}
      </dl>
    </section>
  );
}

export function MessageThread({ conversation, ask, onChanged }: MessageThreadProps) {
  return (
    <div className="flex flex-1 flex-col gap-4 py-4">
      <div className="flex items-center gap-1 text-sm font-medium">
        Verlauf
        <HelpPopover text={HELP.history} />
      </div>
      <FormFacts conversation={conversation} />
      <ol className="flex flex-col gap-3">
        {conversation.messages.map((message) => (
          <MessageBubble
            key={message.id}
            conversationId={conversation.id}
            message={message}
            ask={ask}
            onChanged={onChanged}
          />
        ))}
      </ol>
    </div>
  );
}
