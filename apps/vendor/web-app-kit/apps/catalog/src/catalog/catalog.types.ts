import type { ComponentType } from "react";

export type CatalogGroup = "layout" | "actions" | "forms" | "menus" | "overlays" | "toolbars";

export type CatalogStatus = "stable" | "beta" | "deprecated";

export interface CatalogVariant {
  key: string;
  title: string;
  description: string;
  Preview: ComponentType;
}

export interface CatalogState {
  name: string;
  description: string;
}

export interface CatalogEntry {
  slug: string;
  name: string;
  group: CatalogGroup;
  status: CatalogStatus;
  description: string;
  exports: string[];
  types?: string[];
  variants: CatalogVariant[];
  states: CatalogState[];
  previewMinHeight?: string;
  importPath?: string;
}
