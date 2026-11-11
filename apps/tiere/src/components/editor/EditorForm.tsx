import { PageContainer } from "@web-app-kit/frontend/ui";
import type { EditorFormProps } from "./editor.types";
import { EditorHeader } from "./EditorHeader";
import { SaveBar } from "./SaveBar";
import {
  CompanionsSection,
  DetailsSection,
  InterestSection,
  MediaSection,
  ProfileSection,
  StatusSection,
  StorySection,
  TdwSection,
} from "./sections";
import { useAnimalForm } from "./useAnimalForm";

export function EditorForm({ options, record: initial }: EditorFormProps) {
  const form = useAnimalForm(initial);
  const { draft, record, patch } = form;
  const section = { draft, options, onChange: patch };
  const media = { ...section, animalId: record?.id ?? null };
  const isAdoption = draft.record_type === "vermittlung";

  return (
    <form onSubmit={form.handleSubmit} aria-busy={form.isBusy}>
      <PageContainer size="md" className="flex flex-col gap-8">
        <EditorHeader name={draft.name} record={record} />
        <ProfileSection {...section} />
        <MediaSection {...media} />
        <DetailsSection {...section} />
        <StatusSection {...section} />
        {isAdoption && <StorySection {...media} />}
        {isAdoption && <TdwSection {...media} />}
        {isAdoption && <InterestSection {...section} record={record} />}
        <CompanionsSection {...section} />
      </PageContainer>
      <SaveBar
        {...section}
        record={record}
        isBusy={form.isBusy}
        error={form.error}
        onRestore={form.restore}
      />
    </form>
  );
}
