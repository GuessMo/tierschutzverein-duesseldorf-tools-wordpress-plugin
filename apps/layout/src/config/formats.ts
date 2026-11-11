import type { FormatDefinition, FormatGroup } from "@/types/catalog";

const PRINT_DPI = 300;
const MM_PER_INCH = 25.4;

function toPrintPx(mm: number): number {
  return Math.round((mm / MM_PER_INCH) * PRINT_DPI);
}

function printFormat(
  id: string,
  label: string,
  widthMm: number,
  heightMm: number,
): FormatDefinition {
  return {
    id,
    label,
    group: "print",
    widthMm,
    heightMm,
    widthPx: toPrintPx(widthMm),
    heightPx: toPrintPx(heightMm),
    isAvailable: true,
  };
}

function socialFormat(
  id: string,
  label: string,
  widthPx: number,
  heightPx: number,
): FormatDefinition {
  return { id, label, group: "social", widthPx, heightPx, isAvailable: true };
}

function officeFormat(
  id: string,
  label: string,
  widthMm: number,
  heightMm: number,
): FormatDefinition {
  return { ...printFormat(id, label, widthMm, heightMm), group: "office", isAvailable: false };
}

export const FORMATS: FormatDefinition[] = [
  printFormat("a5", "Flyer A5 hoch", 148, 210),
  printFormat("a6", "Flyer A6 / Postkarte", 105, 148),
  printFormat("a4", "Flyer / Plakat A4", 210, 297),
  printFormat("din-lang", "Flyer DIN lang", 99, 210),
  socialFormat("ig-square", "Instagram Beitrag 1:1", 1080, 1080),
  socialFormat("ig-portrait", "Instagram Beitrag 4:5", 1080, 1350),
  socialFormat("story", "Story / Reel 9:16", 1080, 1920),
  socialFormat("fb-link", "Facebook / Link-Vorschau", 1200, 630),
  officeFormat("business-card", "Visitenkarte", 85, 55),
  officeFormat("letterhead", "Briefbogen A4", 210, 297),
  officeFormat("invoice", "Rechnung A4", 210, 297),
];

export const FORMAT_GROUP_LABELS: Record<FormatGroup, string> = {
  print: "Druck",
  social: "Social Media",
  office: "Geschäftsausstattung (folgt)",
};

export function findFormat(formatId: string): FormatDefinition {
  return FORMATS.find((format) => format.id === formatId) ?? FORMATS[0];
}
