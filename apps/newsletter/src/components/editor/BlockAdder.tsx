import { IconPhoto, IconPlus, IconSchool, IconSparkles, IconTextSize } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import type { BlockKind, BlockType } from "@/types";

const ICONS = {
  rte: IconTextSize,
  image: IconPhoto,
  "website-updates": IconSparkles,
  "academy-updates": IconSchool,
} satisfies Record<BlockKind, unknown>;

interface BlockAdderProps {
  types: BlockType[];
  onAdd: (type: BlockKind) => void;
}

export function BlockAdder({ types, onAdd }: BlockAdderProps) {
  return (
    <div className="flex flex-col gap-2 rounded-xl border border-dashed p-4">
      <span className="flex items-center gap-2 text-sm font-medium">
        <IconPlus aria-hidden className="size-4" />
        Block hinzufügen
      </span>
      <div className="flex flex-wrap gap-2">
        {types.map((type) => {
          const Icon = ICONS[type.type];
          return (
            <Button key={type.type} variant="outline" size="sm" onClick={() => onAdd(type.type)}>
              <Icon data-icon="inline-start" />
              {type.label}
            </Button>
          );
        })}
      </div>
    </div>
  );
}
