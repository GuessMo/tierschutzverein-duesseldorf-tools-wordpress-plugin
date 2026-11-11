import type { BlockData, BlockType, NewsletterBlock } from "@/types";

export interface BlockCardProps {
  block: NewsletterBlock;
  index: number;
  total: number;
  definition: BlockType | undefined;
  onChange: (data: BlockData) => void;
  onMove: (step: number) => void;
  onRemove: () => void;
}
