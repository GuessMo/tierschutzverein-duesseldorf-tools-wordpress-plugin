import { EntryPanel } from "@/components/entries";
import type { Workspace } from "@/hooks";
import type { EditorState } from "./collection.types";

export function entryDetail(workspace: Workspace, editor: EditorState, onClose: () => void) {
  if (!editor) return {};
  const panel = (
    <EntryPanel
      key={editor.entry?.id ?? "new"}
      workspace={workspace}
      onClose={onClose}
      entry={editor.entry}
    />
  );
  return { detail: panel, detailLabel: "Quelle" };
}
