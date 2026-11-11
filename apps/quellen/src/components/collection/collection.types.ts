import type { Entry, Taxonomy, Term } from "@/types";
import type { Workspace } from "@/hooks";

export type TaxonomyDialogState =
  | { type: "none" }
  | { type: "taxonomy-create" }
  | { type: "taxonomy-edit"; taxonomy: Taxonomy }
  | { type: "taxonomy-delete"; taxonomy: Taxonomy }
  | { type: "term-create"; taxonomy: Taxonomy; parentId: number | null }
  | { type: "term-edit"; taxonomy: Taxonomy; term: Term }
  | { type: "term-delete"; term: Term };

export type OpenTaxonomyDialog = (state: TaxonomyDialogState) => void;

export interface WorkspaceProps {
  workspace: Workspace;
}

export interface TaxonomySectionProps {
  taxonomy: Taxonomy;
  terms: Term[];
  onOpen: OpenTaxonomyDialog;
}

export interface TaxonomyDialogsProps extends WorkspaceProps {
  state: TaxonomyDialogState;
  onClose: () => void;
}

export interface MenuAction {
  label: string;
  onSelect: () => void;
  isDestructive?: boolean;
}

export interface DeleteDialogProps {
  onClose: () => void;
  onDeleted: () => Promise<void>;
}

export interface TaxonomyFormProps {
  collectionId: number;
  taxonomy?: Taxonomy;
  onClose: () => void;
  onSaved: () => Promise<void>;
}

export interface ParentSelectProps {
  terms: Term[];
  termId?: number;
  value: number | null;
  onChange: (value: number | null) => void;
}

export interface TaxonomyKindFieldProps {
  isHierarchical: boolean;
  wasHierarchical: boolean;
  onChange: (isHierarchical: boolean) => void;
}

export interface TaxonomyListProps extends WorkspaceProps {
  onOpen: OpenTaxonomyDialog;
  onCreate: () => void;
}

export interface TermFormProps {
  taxonomy: Taxonomy;
  terms: Term[];
  term?: Term;
  parentId?: number | null;
  onClose: () => void;
  onSaved: () => Promise<void>;
}

export type EditorState = { entry?: Entry } | null;

export interface TermRowProps {
  taxonomy: Taxonomy;
  term: Term;
  depth: number;
  onOpen: OpenTaxonomyDialog;
}
