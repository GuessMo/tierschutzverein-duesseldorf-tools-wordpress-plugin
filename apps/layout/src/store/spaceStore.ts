import { create } from "zustand";
import type { ElementKind } from "@/types/document";

interface SpaceState {
  overflowIds: string[];
  notice: string | null;
  placeable: Partial<Record<ElementKind, boolean>>;
  setOverflowIds: (overflowIds: string[]) => void;
  setNotice: (notice: string | null) => void;
  setPlaceable: (placeable: Partial<Record<ElementKind, boolean>>) => void;
}

export const useSpaceStore = create<SpaceState>((set) => ({
  overflowIds: [],
  notice: null,
  placeable: {},
  setOverflowIds: (overflowIds) => set({ overflowIds }),
  setNotice: (notice) => set({ notice }),
  setPlaceable: (placeable) => set({ placeable }),
}));
