import { useEffect, type MouseEvent } from "react";
import { IconArrowLeft } from "@tabler/icons-react";
import { Button, Input, Label, PageContainer } from "@web-app-kit/frontend/ui";
import { newsletterApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { ISSUES_HASH, useIssueEditor, useResource, useUnsavedWarning } from "@/hooks";
import { BlockList } from "./BlockList";
import { SendPanel } from "./SendPanel";

const LEAVE_QUESTION = "Du hast ungespeicherte Änderungen. Trotzdem zur Übersicht wechseln?";

export function IssueEditor({ id }: { id: number }) {
  const editor = useIssueEditor(id);
  const index = useResource(newsletterApi.index);
  const { issue, draft, setDraft, isDirty, isSaving, save } = editor;
  useUnsavedWarning(isDirty);
  useEffect(() => {
    const onKey = (event: KeyboardEvent) => {
      if ((event.metaKey || event.ctrlKey) && event.key === "s") {
        event.preventDefault();
        if (isDirty) void save();
      }
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [isDirty, save]);
  const confirmLeave = (event: MouseEvent<HTMLAnchorElement>) => {
    if (isDirty && !window.confirm(LEAVE_QUESTION)) event.preventDefault();
  };
  if (editor.error)
    return <ErrorMessage message={editor.error} onRetry={() => window.location.reload()} />;
  if (!issue || !index.data) return <LoadingRows label="Newsletter wird geladen" count={5} />;
  return (
    <PageContainer size="lg" className="flex flex-col gap-6">
      <Button asChild variant="ghost" size="sm" className="self-start">
        <a href={ISSUES_HASH} onClick={confirmLeave}>
          <IconArrowLeft data-icon="inline-start" />
          Alle Newsletter
        </a>
      </Button>
      <div className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
        <div className="flex min-w-0 flex-col gap-6">
          <div className="flex flex-col gap-1.5">
            <Label htmlFor="issue-subject">Betreff</Label>
            <Input
              id="issue-subject"
              className="text-lg font-semibold"
              value={draft.title}
              onChange={(event) => setDraft({ ...draft, title: event.target.value })}
            />
          </div>
          <BlockList
            blocks={draft.blocks}
            types={index.data.blockTypes}
            onChange={(blocks) => setDraft({ ...draft, blocks })}
          />
        </div>
        <SendPanel
          issue={issue}
          subscriberCount={index.data.subscriberCount}
          isDirty={isDirty}
          isSaving={isSaving}
          onSave={save}
          onSent={(summary) => editor.setIssue({ ...issue, ...summary })}
        />
      </div>
    </PageContainer>
  );
}
