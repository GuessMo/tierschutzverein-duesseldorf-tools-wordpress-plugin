import { useState } from "react";
import { IconDeviceFloppy, IconEye, IconMailForward, IconSend } from "@tabler/icons-react";
import { Button, Card, CardContent, toast } from "@web-app-kit/frontend/ui";
import { newsletterApi } from "@/api";
import { IssueStatus } from "@/components/issues";
import type { IssueSummary } from "@/types";
import { PreviewDialog } from "./PreviewDialog";
import { SendConfirm } from "./SendConfirm";

interface SendPanelProps {
  issue: IssueSummary;
  subscriberCount: number;
  isDirty: boolean;
  isSaving: boolean;
  onSave: () => Promise<boolean>;
  onSent: (issue: IssueSummary) => void;
}

export function SendPanel({
  issue,
  subscriberCount,
  isDirty,
  isSaving,
  onSave,
  onSent,
}: SendPanelProps) {
  const [preview, setPreview] = useState<string | null>(null);
  const [isConfirming, setIsConfirming] = useState(false);
  const [isBusy, setIsBusy] = useState(false);
  const ready = async () => !isDirty || (await onSave());
  const showPreview = async () => {
    if (!(await ready())) return;
    setPreview((await newsletterApi.preview(issue.id)).html);
  };
  const send = async (mode: "test" | "all") => {
    setIsConfirming(false);
    if (!(await ready())) return;
    setIsBusy(true);
    try {
      const result = await newsletterApi.send(issue.id, mode);
      const text = mode === "test" ? "Testmail an Dich" : "Newsletter";
      toast.success(`${text} versendet: ${result.sent} von ${result.total}.`);
      onSent(result.issue);
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Der Versand ist fehlgeschlagen.");
    } finally {
      setIsBusy(false);
    }
  };
  return (
    <Card className="lg:sticky lg:top-4">
      <CardContent className="flex flex-col gap-3">
        <IssueStatus sentAt={issue.sentAt} />
        <p className="text-sm text-muted-foreground">{subscriberCount} interne Abonnenten.</p>
        <Button onClick={() => void onSave()} disabled={!isDirty || isSaving}>
          <IconDeviceFloppy data-icon="inline-start" />
          {isSaving ? "Wird gespeichert …" : isDirty ? "Speichern" : "Gespeichert"}
        </Button>
        <Button variant="outline" onClick={() => void showPreview()}>
          <IconEye data-icon="inline-start" />
          Vorschau anzeigen
        </Button>
        <hr className="border-border" />
        <Button variant="outline" onClick={() => void send("test")} disabled={isBusy}>
          <IconMailForward data-icon="inline-start" />
          Testmail an mich
        </Button>
        <Button onClick={() => setIsConfirming(true)} disabled={isBusy || subscriberCount === 0}>
          <IconSend data-icon="inline-start" />
          An {subscriberCount} senden
        </Button>
      </CardContent>
      <PreviewDialog html={preview} onClose={() => setPreview(null)} />
      <SendConfirm
        isOpen={isConfirming}
        count={subscriberCount}
        isResend={issue.sentAt !== ""}
        onCancel={() => setIsConfirming(false)}
        onConfirm={() => void send("all")}
      />
    </Card>
  );
}
