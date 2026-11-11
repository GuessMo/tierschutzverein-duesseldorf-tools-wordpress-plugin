import { createCell, createElement, createSection } from "@/services/factories";
import type { Cell, ElementKind, LayoutElement, Section, SplitId } from "@/types/document";
import { CONTACT, WEBSITE } from "./content";

export function el(kind: ElementKind, text: string, overrides: Partial<LayoutElement> = {}) {
  return createElement(kind, { text, ...overrides });
}

export function photo(src: string, overrides: Partial<LayoutElement> = {}) {
  return createElement("image", { imageSrc: src, imageShape: "fill", ...overrides });
}

export function cell(elements: LayoutElement[], overrides: Partial<Cell> = {}) {
  return createCell(elements, overrides);
}

export function section(split: SplitId, cells: Cell[], overrides: Partial<Section> = {}) {
  return createSection(split, { ...overrides, cells });
}

export function headerBar(badge: string): Section {
  return section(
    "twoOne",
    [
      cell([el("logo", "Tierschutzverein Düsseldorf", { size: "s" })], { verticalAlign: "center" }),
      cell([el("badge", badge, { align: "end", size: "s" })], { verticalAlign: "center" }),
    ],
    { tone: "sand", padding: "s" },
  );
}

export function contactFooter(note?: string): Section {
  const lines = [el("contact", CONTACT, { size: "s" })];
  if (note) lines.push(el("text", note, { size: "s", tone: "white" }));
  return section(
    "twoOne",
    [
      cell(lines, { verticalAlign: "center" }),
      cell([el("logo", "", { size: "m", align: "end" })], { verticalAlign: "center" }),
    ],
    { tone: "brown", padding: "m", gap: "s" },
  );
}

export function websiteFooter(tone: Section["tone"] = "green"): Section {
  return section(
    "twoOne",
    [
      cell([el("text", WEBSITE, { size: "l" })], { verticalAlign: "center" }),
      cell([el("logo", "", { size: "s", align: "end" })], { verticalAlign: "center" }),
    ],
    { tone, padding: "s" },
  );
}

export function photoStage(src: string, elements: LayoutElement[]): Section {
  return section("full", [cell(elements, { verticalAlign: "end" })], {
    tone: "brown",
    padding: "l",
    height: "fill",
    backgroundImage: src,
    overlay: true,
  });
}
