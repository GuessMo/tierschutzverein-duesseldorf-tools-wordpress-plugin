import { marked, type Token, type Tokens } from "marked";
import type { Content } from "pdfmake/interfaces";

export const ONLINE_NOTE = "Diese Grafik gibt es nur in der Online-Fassung der Anleitung.";

function plain(text: string): string {
  return text
    .replace(/\*\*(.+?)\*\*/g, "$1")
    .replace(/\*(.+?)\*/g, "$1")
    .replace(/`(.+?)`/g, "$1")
    .replace(/\[(.+?)\]\((.+?)\)/g, "$1 ($2)");
}

function listItems(list: Tokens.List): Content[] {
  return list.items.map((item) => ({ text: plain(item.text), margin: [0, 0, 0, 3] }));
}

function blockFor(token: Token, images: Map<string, string>): Content | null {
  switch (token.type) {
    case "heading":
      return { text: plain(token.text), style: token.depth <= 2 ? "h2" : "h3" };
    case "paragraph":
      if (/^:::[a-z-]+$/.test(token.text.trim())) return { text: ONLINE_NOTE, style: "note" };
      if (token.tokens?.[0]?.type === "image")
        return imageBlock(token.tokens[0] as Tokens.Image, images);
      return { text: plain(token.text), style: "body" };
    case "list":
      return token.ordered
        ? { ol: listItems(token as Tokens.List), style: "body" }
        : { ul: listItems(token as Tokens.List), style: "body" };
    case "code":
      return { text: token.text, style: "code" };
    case "blockquote":
      return { text: plain(token.text), style: "note" };
    default:
      return null;
  }
}

function imageBlock(image: Tokens.Image, images: Map<string, string>): Content {
  const data = images.get(image.href);
  if (!data) return { text: `Bild: ${image.text}`, style: "note" };
  return { image: data, fit: [480, 320], margin: [0, 6, 0, 10] };
}

export function imageUrls(markdown: string): string[] {
  return marked
    .lexer(markdown)
    .flatMap((token) => (token.type === "paragraph" ? (token.tokens ?? []) : []))
    .filter((token): token is Tokens.Image => token.type === "image")
    .map((token) => token.href);
}

export function pdfBlocks(markdown: string, images: Map<string, string>): Content[] {
  return marked
    .lexer(markdown)
    .map((token) => blockFor(token, images))
    .filter((block): block is Content => block !== null);
}
