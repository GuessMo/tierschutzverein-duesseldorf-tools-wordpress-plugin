import type { LayoutDocument } from "@/types/document";

export interface DocumentPreviewProps {
  document: LayoutDocument;
  maxWidth: number;
  maxHeight: number;
}

export interface TemplateCardProps {
  preview: LayoutDocument;
}
