import type { FormEvent } from "react";
import type { AnimalDraft, AnimalOptions, AnimalRecord, DraftPatch } from "@/types";

export interface AnimalEditorProps {
  options: AnimalOptions;
  animalId: number | null;
}

export interface EditorFormProps {
  options: AnimalOptions;
  record: AnimalRecord | null;
}

export interface SectionProps {
  draft: AnimalDraft;
  options: AnimalOptions;
  onChange: (patch: DraftPatch) => void;
}

export interface MediaSectionProps extends SectionProps {
  animalId: number | null;
}

export interface InterestSectionProps extends SectionProps {
  record: AnimalRecord | null;
}

export interface EditorHeaderProps {
  name: string;
  record: AnimalRecord | null;
}

export interface SaveBarProps {
  draft: AnimalDraft;
  options: AnimalOptions;
  record: AnimalRecord | null;
  isBusy: boolean;
  error: string | null;
  onChange: (patch: DraftPatch) => void;
  onRestore: () => void;
}

export interface AnimalForm {
  draft: AnimalDraft;
  record: AnimalRecord | null;
  isBusy: boolean;
  error: string | null;
  patch: (patch: DraftPatch) => void;
  handleSubmit: (event: FormEvent) => void;
  restore: () => void;
}
