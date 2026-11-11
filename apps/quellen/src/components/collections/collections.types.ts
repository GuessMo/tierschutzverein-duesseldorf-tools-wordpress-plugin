import type { Collection, CollectionInput, Resource } from "@/types";

export type CollectionDialogState =
  | { type: "none" }
  | { type: "create" }
  | { type: "edit"; collection: Collection }
  | { type: "delete"; collection: Collection };

export interface CollectionTableProps {
  collections: Collection[];
  onEdit: (collection: Collection) => void;
  onDelete: (collection: Collection) => void;
}

export interface CollectionDialogsProps {
  state: CollectionDialogState;
  onClose: () => void;
  onChanged: () => Promise<void>;
}

export interface CollectionFormProps {
  collection?: Collection;
  onClose: () => void;
  onSaved: () => Promise<void>;
}

export interface CollectionRowProps {
  collection: Collection;
  onEdit: (collection: Collection) => void;
  onDelete: (collection: Collection) => void;
}

export interface CollectionContentProps {
  collections: Resource<Collection[]>;
  onEdit: (collection: Collection) => void;
  onDelete: (collection: Collection) => void;
}

export interface CollectionFieldsProps extends CollectionInput {
  onChange: (input: CollectionInput) => void;
}
