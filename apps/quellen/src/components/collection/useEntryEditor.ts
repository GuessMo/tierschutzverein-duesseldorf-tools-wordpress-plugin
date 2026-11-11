import { useCallback, useState } from "react";
import type { Entry } from "@/types";
import type { EditorState } from "./collection.types";

export function useEntryEditor() {
  const [editor, setEditor] = useState<EditorState>(null);
  const close = useCallback(() => setEditor(null), []);
  const openNew = useCallback(() => setEditor({}), []);
  const openEntry = useCallback((entry: Entry) => setEditor({ entry }), []);
  return { editor, close, openNew, openEntry };
}
