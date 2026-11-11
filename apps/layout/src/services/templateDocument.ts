import { createPage } from "@/services/pageOperations";
import type { DocumentTemplate } from "@/types/catalog";
import type { LayoutDocument } from "@/types/document";

export function documentFromTemplate(template: DocumentTemplate, id: string): LayoutDocument {
  const pages = [createPage(template.createSections())];
  if (template.createBackSections) pages.push(createPage(template.createBackSections()));
  return {
    id,
    name: template.label,
    formatId: template.formatId,
    orientation: template.orientation,
    tone: template.tone,
    pages,
    updatedAt: new Date().toISOString(),
  };
}
