import { createSection } from "@/services/factories";
import { cloneSection } from "@/services/sectionOperations";
import type { LayoutDocument, Page, Section } from "@/types/document";
import { insertAfter, moveItem } from "@/utils/arrays";
import { createId } from "@/utils/id";

export function createPage(
  sections: Section[] = [createSection("full", { height: "fill" })],
): Page {
  return { id: createId(), sections };
}

export function mapPage(document: LayoutDocument, pageId: string, update: (page: Page) => Page) {
  return {
    ...document,
    pages: document.pages.map((page) => (page.id === pageId ? update(page) : page)),
  };
}

export function addPage(document: LayoutDocument, page: Page, afterPageId: string): LayoutDocument {
  const index = document.pages.findIndex((entry) => entry.id === afterPageId);
  return { ...document, pages: insertAfter(document.pages, index, page) };
}

export function clonePage(page: Page): Page {
  return { id: createId(), sections: page.sections.map(cloneSection) };
}

export function movePage(document: LayoutDocument, pageId: string, delta: number): LayoutDocument {
  const index = document.pages.findIndex((entry) => entry.id === pageId);
  return { ...document, pages: moveItem(document.pages, index, delta) };
}

export function removePage(document: LayoutDocument, pageId: string): LayoutDocument {
  if (document.pages.length <= 1) return document;
  return { ...document, pages: document.pages.filter((entry) => entry.id !== pageId) };
}

export function normalizeDocument(
  stored: LayoutDocument & { sections?: Section[] },
): LayoutDocument {
  if (stored.pages?.length) return stored;
  const { sections = [], ...rest } = stored;
  return { ...rest, pages: [createPage(sections)] };
}
