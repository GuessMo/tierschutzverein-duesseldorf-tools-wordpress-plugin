import { create } from "zustand";
import type { ContrastIssue } from "@/types/contrast";

interface ContrastState {
  issues: ContrastIssue[];
  setIssues: (issues: ContrastIssue[]) => void;
}

export const useContrastStore = create<ContrastState>((set) => ({
  issues: [],
  setIssues: (issues) => set({ issues }),
}));
