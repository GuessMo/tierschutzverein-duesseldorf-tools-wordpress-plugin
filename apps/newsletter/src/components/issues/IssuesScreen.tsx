import { useState } from "react";
import { IconPlus } from "@tabler/icons-react";
import { Button, PageContainer, toast } from "@web-app-kit/frontend/ui";
import { newsletterApi } from "@/api";
import { ErrorMessage, LoadingRows, StateMessage } from "@/components/common";
import { issueHash, useResource } from "@/hooks";
import { IssueRow } from "./IssueRow";

function countLabel(count: number): string {
  return count === 1 ? "1 Ausgabe" : `${count} Ausgaben`;
}

function summaryLabel(issues: number, subscribers: number): string {
  return `${countLabel(issues)} · ${subscribers} interne Abonnenten`;
}

export function IssuesScreen() {
  const index = useResource(newsletterApi.index);
  const [isCreating, setIsCreating] = useState(false);
  const create = async () => {
    setIsCreating(true);
    try {
      const issue = await newsletterApi.create("");
      window.location.hash = issueHash(issue.id);
    } catch {
      toast.error("Der Newsletter konnte nicht angelegt werden.");
      setIsCreating(false);
    }
  };
  return (
    <PageContainer size="lg" className="flex flex-col gap-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-2xl font-semibold">Newsletter</h1>
        <Button onClick={() => void create()} disabled={isCreating}>
          <IconPlus data-icon="inline-start" />
          Neuer Newsletter
        </Button>
      </div>
      {index.error && <ErrorMessage message={index.error} onRetry={() => void index.reload()} />}
      {!index.data && !index.error && <LoadingRows label="Newsletter werden geladen" />}
      {index.data && index.data.issues.length === 0 && (
        <StateMessage title="Noch kein Newsletter" description="Lege oben den ersten an." />
      )}
      {index.data && index.data.issues.length > 0 && (
        <div className="flex flex-col gap-3">
          <p className="text-sm text-muted-foreground">
            {summaryLabel(index.data.issues.length, index.data.subscriberCount)}
          </p>
          <ul className="flex flex-col gap-1">
            {index.data.issues.map((issue) => (
              <IssueRow key={issue.id} issue={issue} />
            ))}
          </ul>
        </div>
      )}
    </PageContainer>
  );
}
