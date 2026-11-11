import { useEffect } from "react";
import { EditorScreen } from "@/components/editor";
import { LibraryScreen } from "@/components/library";
import { PrintSheets } from "@/components/print/PrintSheets";
import { useEditorStore } from "@/store/editorStore";
import { useLibraryStore } from "@/store/libraryStore";

export function App() {
  const openDocumentId = useLibraryStore((state) => state.openDocumentId);
  const isEditing = useEditorStore(
    (state) => state.document?.id === openDocumentId && openDocumentId !== null,
  );

  useEffect(() => {
    void useLibraryStore.getState().load();
  }, []);

  useEffect(() => {
    const document = useLibraryStore
      .getState()
      .documents.find((entry) => entry.id === openDocumentId);
    if (document) useEditorStore.getState().load(document);
  }, [openDocumentId]);

  return (
    <>
      {isEditing ? <EditorScreen /> : <LibraryScreen />}
      <PrintSheets />
    </>
  );
}
