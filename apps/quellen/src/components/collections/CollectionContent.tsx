import { ErrorMessage, LoadingRows, StateMessage } from "@/components/common";
import { TEXTS } from "@/config/texts";
import type { CollectionContentProps } from "./collections.types";
import { CollectionTable } from "./CollectionTable";

export function CollectionContent({ collections, onEdit, onDelete }: CollectionContentProps) {
  if (collections.error) {
    return <ErrorMessage message={collections.error} onRetry={() => void collections.reload()} />;
  }
  if (!collections.data) return <LoadingRows label="Sammlungen werden geladen" />;
  if (collections.data.length === 0) {
    return <StateMessage title="Noch keine Sammlung" description={TEXTS.collectionsEmpty} />;
  }
  return <CollectionTable collections={collections.data} onEdit={onEdit} onDelete={onDelete} />;
}
