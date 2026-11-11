import { useState } from "react";
import type { ListView } from "@/types";

const STORAGE_KEY = "tsvd-tiere-view";

function readView(): ListView {
  try {
    return localStorage.getItem(STORAGE_KEY) === "tiles" ? "tiles" : "list";
  } catch {
    return "list";
  }
}

function storeView(view: ListView): void {
  try {
    localStorage.setItem(STORAGE_KEY, view);
  } catch {
    return;
  }
}

export function useListView(): [ListView, (view: ListView) => void] {
  const [view, setView] = useState<ListView>(readView);
  const change = (next: ListView) => {
    setView(next);
    storeView(next);
  };
  return [view, change];
}
