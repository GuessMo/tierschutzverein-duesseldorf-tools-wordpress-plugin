import { createBlock, moveBlock } from "@/lib";
import type { BlockType, NewsletterBlock } from "@/types";
import { BlockAdder } from "./BlockAdder";
import { BlockCard } from "./BlockCard";

interface BlockListProps {
  blocks: NewsletterBlock[];
  types: BlockType[];
  onChange: (blocks: NewsletterBlock[]) => void;
}

export function BlockList({ blocks, types, onChange }: BlockListProps) {
  const replace = (index: number, block: NewsletterBlock) =>
    onChange(blocks.map((current, position) => (position === index ? block : current)));
  return (
    <div className="flex flex-col gap-4">
      {blocks.length === 0 && (
        <p className="text-sm text-muted-foreground">
          Noch keine Blöcke. Füge unten den ersten hinzu.
        </p>
      )}
      {blocks.map((block, index) => (
        <BlockCard
          key={block.id}
          block={block}
          index={index}
          total={blocks.length}
          definition={types.find((type) => type.type === block.type)}
          onChange={(data) => replace(index, { ...block, data })}
          onMove={(step) => onChange(moveBlock(blocks, index, step))}
          onRemove={() => onChange(blocks.filter((_, position) => position !== index))}
        />
      ))}
      <BlockAdder types={types} onAdd={(type) => onChange([...blocks, createBlock(type)])} />
    </div>
  );
}
