import { PageContainer } from "@web-app-kit/frontend/ui";
import type { EditorFormProps } from "./editor.types";
import { EditorHeader } from "./EditorHeader";
import { EditorTabs } from "./EditorTabs";
import { SaveBar } from "./SaveBar";
import { useAnimalForm } from "./useAnimalForm";

export function EditorForm({ options, record: initial }: EditorFormProps) {
  const form = useAnimalForm(initial);
  const { draft, record, patch } = form;
  const section = { draft, options, onChange: patch };
  const media = { ...section, animalId: record?.id ?? null };

  return (
    <form
      onSubmit={form.handleSubmit}
      aria-busy={form.isBusy}
      className="flex min-h-full flex-col"
    >
      <PageContainer size="md" className="flex w-full flex-1 flex-col gap-8">
        <EditorHeader {...section} record={record} />
        <EditorTabs section={section} media={media} record={record} />
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
