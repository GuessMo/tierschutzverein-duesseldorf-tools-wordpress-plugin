import { Marked, type Tokens } from "marked";
import type { RenderedLesson, TocItem } from "@/types";

const BLOCK_PATTERN = /^:::([a-z-]+)\s*$/gm;

function slugify(text: string): string {
  const base = text
    .toLowerCase()
    .normalize("NFKD")
    .replace(/[̀-ͯ]/g, "")
    .replace(/ß/g, "ss")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");
  return base || "abschnitt";
}

function createParser(toc: TocItem[]) {
  const used = new Map<string, number>();
  const marked = new Marked({ gfm: true });
  marked.use({
    renderer: {
      heading(this: { parser: { parseInline: (t: Tokens.Generic[]) => string } }, token) {
        const inner = this.parser.parseInline(token.tokens);
        const base = slugify(token.text);
        const count = used.get(base) ?? 0;
        used.set(base, count + 1);
        const id = count ? `${base}-${count + 1}` : base;
        if (token.depth === 2) toc.push({ id, text: token.text });
        return `<h${token.depth} id="${id}">${inner}</h${token.depth}>\n`;
      },
      link(this: { parser: { parseInline: (t: Tokens.Generic[]) => string } }, token) {
        const inner = this.parser.parseInline(token.tokens);
        return `<a href="${token.href}" target="_blank" rel="noreferrer">${inner}</a>`;
      },
    },
  });
  return marked;
}

export function renderLesson(markdown: string, blocks: Record<string, string>): RenderedLesson {
  const toc: TocItem[] = [];
  const withBlocks = markdown.replace(BLOCK_PATTERN, (_, name: string) => {
    const html = blocks[name];
    return html ? `\n<div class="academy-block">${html}</div>\n` : "";
  });
  const html = createParser(toc).parse(withBlocks, { async: false });
  return { html, toc };
}
