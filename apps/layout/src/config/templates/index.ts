import { createSection } from "@/services/factories";
import type { DocumentTemplate } from "@/types/catalog";
import { POSTCARD_TEMPLATES } from "./postcards";
import { POSTER_TEMPLATES } from "./posters";
import { PRINT_TEMPLATES } from "./print";
import { SOCIAL_TEMPLATES } from "./social";
import { CHANNEL_TEMPLATES } from "./channels";

const BLANK_TEMPLATE: DocumentTemplate = {
  id: "blank",
  label: "Leeres Dokument",
  formatId: "a5",
  createSections: () => [createSection("full", { height: "fill" })],
};

export const TEMPLATES: DocumentTemplate[] = [
  ...PRINT_TEMPLATES,
  ...POSTCARD_TEMPLATES,
  ...POSTER_TEMPLATES,
  ...SOCIAL_TEMPLATES,
  ...CHANNEL_TEMPLATES,
  BLANK_TEMPLATE,
];
