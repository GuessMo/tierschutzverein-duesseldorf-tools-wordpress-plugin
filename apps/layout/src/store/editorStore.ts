import { create } from "zustand";
import { mapPage } from "@/services/pageOperations";
import type { Selection } from "@/types/catalog";
import type { LayoutDocument, Page } from "@/types/document";
import type { EditorAction } from "@/types/editor";

const HISTORY_LIMIT = 50;

interface EditorState {
  document: LayoutDocument | null;
  activePageId: string;
  selection: Selection;
  past: LayoutDocument[];
  future: LayoutDocument[];
  lastAction: EditorAction;
  load: (document: LayoutDocument) => void;
  apply: (update: (document: LayoutDocument) => LayoutDocument) => void;
  applyPage: (update: (page: Page) => Page) => void;
  showPage: (pageId: string) => void;
  select: (selection: Selection) => void;
  undo: () => void;
  redo: () => void;
}

function touch(document: LayoutDocument): LayoutDocument {
  return { ...document, updatedAt: new Date().toISOString() };
}

function keepPage(document: LayoutDocument, pageId: string): string {
  return document.pages.some((page) => page.id === pageId) ? pageId : document.pages[0].id;
}

export const useEditorStore = create<EditorState>((set, get) => ({
  document: null,
  activePageId: "",
  selection: { type: "none" },
  past: [],
  future: [],
  lastAction: "load",
  load: (document) =>
    set({
      document,
      activePageId: document.pages[0].id,
      selection: { type: "none" },
      past: [],
      future: [],
      lastAction: "load",
    }),
  apply: (update) => {
    const current = get().document;
    if (!current) return;
    const next = update(current);
    if (next === current) return;
    set({
      document: touch(next),
      activePageId: keepPage(next, get().activePageId),
      past: [...get().past, current].slice(-HISTORY_LIMIT),
      future: [],
      lastAction: "edit",
    });
  },
  applyPage: (update) => get().apply((document) => mapPage(document, get().activePageId, update)),
  showPage: (pageId) =>
    set({ activePageId: pageId, selection: { type: "none" }, lastAction: "load" }),
  select: (selection) => set({ selection }),
  undo: () => {
    const { past, document, future, activePageId } = get();
    if (!document || past.length === 0) return;
    const previous = past[past.length - 1];
    set({
      document: touch(previous),
      activePageId: keepPage(previous, activePageId),
      past: past.slice(0, -1),
      future: [document, ...future],
      lastAction: "history",
    });
  },
  redo: () => {
    const { past, document, future, activePageId } = get();
    if (!document || future.length === 0) return;
    set({
      document: touch(future[0]),
      activePageId: keepPage(future[0], activePageId),
      past: [...past, document],
      future: future.slice(1),
      lastAction: "history",
    });
  },
}));

export function revertLastEdit(): void {
  const { past, activePageId } = useEditorStore.getState();
  const previous = past[past.length - 1];
  if (!previous) return;
  useEditorStore.setState({
    document: previous,
    activePageId: keepPage(previous, activePageId),
    past: past.slice(0, -1),
    lastAction: "history",
  });
}

export function selectActivePage(state: EditorState): Page | null {
  return state.document?.pages.find((page) => page.id === state.activePageId) ?? null;
}
