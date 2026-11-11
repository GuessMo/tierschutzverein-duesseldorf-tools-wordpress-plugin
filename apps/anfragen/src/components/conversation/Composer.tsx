import { useState, type KeyboardEvent } from "react";
import { Button, Label, Tabs, TabsList, TabsTrigger, Textarea } from "@web-app-kit/frontend/ui";
import { messagesApi } from "@/api";
import { HelpPopover } from "@/components/common";
import { HELP } from "@/config/help";
import { runAction } from "@/lib";
import type { ComposerMode } from "@/types";
import type { ComposerProps } from "./conversation.types";

const MODES: ComposerMode[] = ["reply", "note"];

export function Composer({ conversation, onSent }: ComposerProps) {
  const [mode, setMode] = useState<ComposerMode>("reply");
  const [body, setBody] = useState("");
  const [isSending, setIsSending] = useState(false);
  const text = conversation.composer[mode];
  const submit = async () => {
    if (!body.trim() || isSending) return;
    setIsSending(true);
    const isDone = await runAction(() => messagesApi.create(conversation.id, mode, body), onSent);
    if (isDone) setBody("");
    setIsSending(false);
  };
  const onKeyDown = (event: KeyboardEvent) => {
    if ((event.metaKey || event.ctrlKey) && event.key === "Enter") void submit();
  };
  return (
    <div className="sticky bottom-0 flex flex-col gap-2 border-t bg-background pt-3 pb-4">
      <div className="flex items-center justify-between gap-2">
        <Tabs value={mode} onValueChange={(next) => setMode(next as ComposerMode)}>
          <TabsList aria-label="Art der Nachricht">
            {MODES.map((item) => (
              <TabsTrigger key={item} value={item}>
                {conversation.composer[item].tab}
              </TabsTrigger>
            ))}
          </TabsList>
        </Tabs>
        <HelpPopover text={HELP.composer} />
      </div>
      <Label htmlFor="anfrage-composer" className="sr-only">
        {text.label}
      </Label>
      <Textarea
        id="anfrage-composer"
        rows={4}
        placeholder={text.label}
        value={body}
        onKeyDown={onKeyDown}
        onChange={(event) => setBody(event.target.value)}
      />
      <div className="flex items-center justify-between gap-3">
        <span className="text-xs text-muted-foreground">{text.hint}</span>
        <Button disabled={!body.trim() || isSending} onClick={() => void submit()}>
          {isSending ? "Sendet …" : text.button}
        </Button>
      </div>
    </div>
  );
}
