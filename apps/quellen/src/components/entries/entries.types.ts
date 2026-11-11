import type { Entry, Taxonomy, Term } from "@/types";
import type { EntryDraft } from "@/types/draft";
import type { EntryFilter } from "@/types/filter";
import type { Workspace } from "@/hooks";

export interface EntriesViewProps {
  workspace: Workspace;
  onCreate: () => void;
  onEdit: (entry: Entry) => void;
}

export interface EntryFiltersProps {
  taxonomies: Taxonomy[];
  terms: Term[];
  filter: EntryFilter;
  onChange: (filter: EntryFilter) => void;
}

export interface EntryListProps {
  entries: Entry[];
  terms: Term[];
  onEdit: (entry: Entry) => void;
  onDelete: (entry: Entry) => void;
}

export interface EntryItemProps {
  entry: Entry;
  terms: Term[];
  onEdit: (entry: Entry) => void;
  onDelete: (entry: Entry) => void;
}

export interface EntryPanelProps {
  workspace: Workspace;
  entry?: Entry;
  onClose: () => void;
}

export interface DraftFieldsProps {
  draft: EntryDraft;
  onChange: (patch: Partial<EntryDraft>) => void;
}

export interface TermPickerProps {
  taxonomies: Taxonomy[];
  terms: Term[];
  selected: number[];
  onChange: (selected: number[]) => void;
}

export interface EntryResultsProps {
  total: number;
  entries: Entry[];
  terms: Term[];
  actions: EntryActions;
}

export interface EntryDeleteDialogProps {
  entry: Entry;
  onClose: () => void;
  onDeleted: () => Promise<void>;
}

export interface TaxonomyFilterProps {
  taxonomy: Taxonomy;
  terms: Term[];
  value: number | undefined;
  onChange: (value: number | undefined) => void;
}

export interface FormActionsProps {
  isBusy: boolean;
  error: string | null;
  onCancel: () => void;
}

export interface EntryActions {
  onCreate: () => void;
  onEdit: (entry: Entry) => void;
  onDelete: (entry: Entry) => void;
}

export interface EntriesBodyProps {
  workspace: Workspace;
  filter: EntryFilter;
  actions: EntryActions;
}

export interface TermGroupProps {
  taxonomy: Taxonomy;
  terms: Term[];
  selected: number[];
  onToggle: (id: number, isChecked: boolean) => void;
}
