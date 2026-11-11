import { useCallback } from "react";
import { PageContainer } from "@web-app-kit/frontend/ui";
import { animalsApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { useResource } from "@/hooks";
import type { AnimalOptions } from "@/types";
import type { AnimalEditorProps } from "./editor.types";
import { EditorForm } from "./EditorForm";

function ExistingAnimal({ options, animalId }: { options: AnimalOptions; animalId: number }) {
  const load = useCallback(() => animalsApi.get(animalId), [animalId]);
  const record = useResource(load);
  if (record.data) return <EditorForm options={options} record={record.data} />;
  return (
    <PageContainer size="md">
      {record.error ? (
        <ErrorMessage message={record.error} onRetry={() => void record.reload()} />
      ) : (
        <LoadingRows label="Tier wird geladen" count={8} />
      )}
    </PageContainer>
  );
}

export function AnimalEditor({ options, animalId }: AnimalEditorProps) {
  if (animalId === null) return <EditorForm options={options} record={null} />;
  return <ExistingAnimal options={options} animalId={animalId} />;
}
