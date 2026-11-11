import { create } from "zustand";

interface PrintState {
  isPrinting: boolean;
  setPrinting: (isPrinting: boolean) => void;
}

export const usePrintStore = create<PrintState>((set) => ({
  isPrinting: false,
  setPrinting: (isPrinting) => set({ isPrinting }),
}));
