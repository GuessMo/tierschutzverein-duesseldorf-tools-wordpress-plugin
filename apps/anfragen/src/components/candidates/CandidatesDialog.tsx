import { useState } from "react";
import { IconInbox } from "@tabler/icons-react";
import {
  Button,
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@web-app-kit/frontend/ui";
import { candidatesApi } from "@/api";
import { ErrorMessage, LoadingRows, StateMessage } from "@/components/common";
import { useResource } from "@/hooks";
import { runAction } from "@/lib";
import type { CandidatesDialogProps } from "./candidates.types";
import { CandidateRow } from "./CandidateRow";

function CandidateList({ onChanged }: CandidatesDialogProps) {
  const candidates = useResource(candidatesApi.list);
  const decide = (id: number, decision: "approve" | "reject") =>
    void runAction(() => candidatesApi.decide(id, decision), async () => {
      await candidates.reload();
      await onChanged();
    });
  if (candidates.error) {
    return <ErrorMessage message={candidates.error} onRetry={() => void candidates.reload()} />;
  }
  if (!candidates.data) return <LoadingRows label="Kandidaten werden geladen" count={3} />;
  if (candidates.data.length === 0) {
    return <StateMessage title="Nichts zu prüfen" description="Alle eingegangenen Mails sind zugeordnet." />;
  }
  return (
    <ul className="flex max-h-[60vh] flex-col gap-2 overflow-y-auto">
      {candidates.data.map((candidate) => (
        <CandidateRow key={candidate.id} candidate={candidate} onDecide={decide} />
      ))}
    </ul>
  );
}

export function CandidatesDialog(props: CandidatesDialogProps) {
  const [isOpen, setIsOpen] = useState(false);
  return (
    <Dialog open={isOpen} onOpenChange={setIsOpen}>
      <DialogTrigger asChild>
        <Button variant="outline" size="sm">
          <IconInbox data-icon="inline-start" />
          Eingang prüfen
        </Button>
      </DialogTrigger>
      <DialogContent closeLabel="Schließen" className="sm:max-w-xl">
        <DialogHeader>
          <DialogTitle>Eingegangene Mails prüfen</DialogTitle>
          <DialogDescription>
            Ordne Antworten per Mail einer Anfrage zu oder verwirf sie, wenn sie nicht dazugehören.
          </DialogDescription>
        </DialogHeader>
        {isOpen && <CandidateList {...props} />}
      </DialogContent>
    </Dialog>
  );
}
