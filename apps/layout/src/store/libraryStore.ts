import { create } from "zustand";
import { TEMPLATES } from "@/config/templates";
import { normalizeDocument } from "@/services/pageOperations";
import { deleteDocument, loadDocuments, saveDocument } from "@/services/storage";
import { documentFromTemplate } from "@/services/templateDocument";
import type { LayoutDocument } from "@/types/document";
import { createId } from "@/utils/id";

interface LibraryState {
  documents: LayoutDocument[];
  openDocumentId: string | null;
  errorMessage: string | null;
  load: () => Promise<void>;
  createFromTemplate: (templateId: string) => Promise<void>;
  persist: (document: LayoutDocument) => Promise<void>;
  remove: (documentId: string) => Promise<void>;
  open: (documentId: string | null) => void;
}

function sortByUpdate(documents: LayoutDocument[]): LayoutDocument[] {
  return [...documents].sort((first, second) => second.updatedAt.localeCompare(first.updatedAt));
}

export const useLibraryStore = create<LibraryState>((set, get) => ({
  documents: [],
  openDocumentId: null,
  errorMessage: null,
  load: async () => {
    try {
      set({ documents: sortByUpdate((await loadDocuments()).map(normalizeDocument)) });
    } catch (error) {
      set({ errorMessage: (error as Error).message });
    }
  },
  createFromTemplate: async (templateId) => {
    const template = TEMPLATES.find((entry) => entry.id === templateId);
    if (!template) return;
    const document = documentFromTemplate(template, createId());
    await get().persist(document);
    set({ openDocumentId: document.id });
  },
  persist: async (document) => {
    try {
      await saveDocument(document);
      const others = get().documents.filter((entry) => entry.id !== document.id);
      set({ documents: sortByUpdate([document, ...others]), errorMessage: null });
    } catch (error) {
      set({ errorMessage: (error as Error).message });
    }
  },
  remove: async (documentId) => {
    await deleteDocument(documentId);
    set({ documents: get().documents.filter((entry) => entry.id !== documentId) });
  },
  open: (documentId) => set({ openDocumentId: documentId }),
}));
