import type { TDocumentDefinitions } from "pdfmake/interfaces";
import type { Lesson } from "@/types";
import { imageUrls, pdfBlocks } from "./pdfContent";

const STYLES: TDocumentDefinitions["styles"] = {
  title: { fontSize: 20, bold: true, margin: [0, 0, 0, 4] },
  meta: { fontSize: 9, color: "#6b6b6b", margin: [0, 0, 0, 16] },
  h2: { fontSize: 14, bold: true, margin: [0, 14, 0, 6] },
  h3: { fontSize: 12, bold: true, margin: [0, 10, 0, 4] },
  body: { fontSize: 10.5, lineHeight: 1.35, margin: [0, 0, 0, 8] },
  code: { fontSize: 9, background: "#f2f2f2", margin: [0, 2, 0, 10] },
  table: { fontSize: 8.5, margin: [0, 2, 0, 12] },
  wideTable: { fontSize: 6.5, margin: [0, 2, 0, 12] },
  note: { fontSize: 10, italics: true, color: "#555555", margin: [0, 2, 0, 10] },
};

function germanDate(iso: string): string {
  const [year, month, day] = iso.split("-");
  return year && month && day ? `${day}.${month}.${year}` : iso;
}

async function toDataUrl(url: string): Promise<string | null> {
  try {
    const response = await fetch(url, { credentials: "same-origin" });
    if (!response.ok) return null;
    const blob = await response.blob();
    return await new Promise((resolve) => {
      const reader = new FileReader();
      reader.onload = () => resolve(typeof reader.result === "string" ? reader.result : null);
      reader.onerror = () => resolve(null);
      reader.readAsDataURL(blob);
    });
  } catch {
    return null;
  }
}

async function loadImages(markdown: string): Promise<Map<string, string>> {
  const entries = await Promise.all(
    imageUrls(markdown).map(async (url) => [url, await toDataUrl(url)] as const),
  );
  return new Map(entries.filter((entry): entry is [string, string] => entry[1] !== null));
}

function definition(lesson: Lesson, images: Map<string, string>): TDocumentDefinitions {
  const topics = lesson.topics.map((topic) => topic.name).join(", ");
  return {
    info: { title: lesson.title, author: "Tierschutzverein Düsseldorf" },
    pageMargins: [56, 56, 56, 56],
    content: [
      { text: lesson.title, style: "title" },
      { text: `Academy · ${topics} · Stand ${germanDate(lesson.updated)}`, style: "meta" },
      ...pdfBlocks(lesson.markdown, images),
    ],
    footer: (page, pages) => ({
      text: `${lesson.title} · Seite ${page} von ${pages}`,
      fontSize: 8,
      color: "#888888",
      margin: [56, 16, 56, 0],
    }),
    styles: STYLES,
  };
}

export async function downloadLessonPdf(lesson: Lesson): Promise<void> {
  const [{ default: pdfMake }, { default: fonts }, images] = await Promise.all([
    import("pdfmake/build/pdfmake"),
    import("pdfmake/build/vfs_fonts"),
    loadImages(lesson.markdown),
  ]);
  const fontModule = fonts as unknown as { vfs?: Record<string, string> } & Record<string, string>;
  pdfMake.vfs = fontModule.vfs ?? fontModule;
  pdfMake.createPdf(definition(lesson, images)).download(`${lesson.slug}.pdf`);
}
