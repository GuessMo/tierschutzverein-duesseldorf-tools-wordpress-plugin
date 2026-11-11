import type { Candidate } from "@/types";

export interface CandidatesDialogProps {
  onChanged: () => Promise<void>;
}

export interface CandidateRowProps {
  candidate: Candidate;
  onDecide: (id: number, decision: "approve" | "reject") => void;
}
