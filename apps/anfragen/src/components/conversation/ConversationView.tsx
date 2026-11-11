import { useCallback } from "react";
import { anfragenApi } from "@/api";
import { ConfirmDialog, ErrorMessage, LoadingRows } from "@/components/common";
import { CONFIRMATIONS } from "@/config/confirmations";
import { useConfirm, useResource } from "@/hooks";
import { runAction } from "@/lib";
import type { ActionResult } from "@/types";
import { Composer } from "./Composer";
import { ConversationHeader } from "./ConversationHeader";
import type { ConversationViewProps, MenuAction } from "./conversation.types";
import { MessageThread } from "./MessageThread";

function actionCall(id: number, action: MenuAction): () => Promise<ActionResult> {
  if (action === "delete") return () => anfragenApi.remove(id);
  return () => anfragenApi.run(id, action);
}

export function ConversationView({ id, assignees, onChanged }: ConversationViewProps) {
  const load = useCallback(() => anfragenApi.get(id), [id]);
  const conversation = useResource(load);
  const confirm = useConfirm();
  const refresh = useCallback(async () => {
    await conversation.reload();
    await onChanged();
  }, [conversation, onChanged]);
  const onAction = (action: MenuAction) => {
    const run = () => runAction(actionCall(id, action), refresh);
    const confirmation = CONFIRMATIONS[action];
    if (confirmation) confirm.ask(confirmation, run);
    else void run();
  };
  if (conversation.error) {
    return <ErrorMessage message={conversation.error} onRetry={() => void conversation.reload()} />;
  }
  if (!conversation.data) return <LoadingRows label="Anfrage wird geladen" count={5} />;
  return (
    <div className="flex min-h-full flex-col">
      <ConversationHeader
        conversation={conversation.data}
        assignees={assignees}
        onAction={onAction}
        onAssign={(userId) => void runAction(() => anfragenApi.assign(id, userId), refresh)}
      />
      <MessageThread conversation={conversation.data} ask={confirm.ask} onChanged={refresh} />
      <Composer conversation={conversation.data} onSent={refresh} />
      <ConfirmDialog
        confirmation={confirm.confirmation}
        onCancel={confirm.cancel}
        onConfirm={confirm.confirm}
      />
    </div>
  );
}
