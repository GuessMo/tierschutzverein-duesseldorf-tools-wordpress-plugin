import type { BlockKind, NewsletterBlock } from "@/types";

export function createBlock(type: BlockKind): NewsletterBlock {
  const id = `blk_${Date.now().toString(36)}${Math.random().toString(36).slice(2, 6)}`;
  return { id, type, data: type === "rte" ? { html: "" } : {} };
}

export function moveBlock(
  blocks: NewsletterBlock[],
  index: number,
  step: number,
): NewsletterBlock[] {
  const target = index + step;
  if (target < 0 || target >= blocks.length) return blocks;
  const next = [...blocks];
  const [item] = next.splice(index, 1);
  if (item) next.splice(target, 0, item);
  return next;
}
