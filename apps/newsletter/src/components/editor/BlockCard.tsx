import { IconArrowDown, IconArrowUp, IconTrash } from "@tabler/icons-react";
import { Button, Card, CardContent, CardHeader, CardTitle } from "@web-app-kit/frontend/ui";
import type { BlockCardProps } from "./editor.types";
import { ImageField } from "./ImageField";
import { RichTextField } from "./RichTextField";

function BlockBody({
  block,
  definition,
  onChange,
}: Pick<BlockCardProps, "block" | "definition" | "onChange">) {
  if (block.type === "rte") {
    return (
      <RichTextField
        id={`block-${block.id}`}
        label="Text"
        html={block.data.html ?? ""}
        onChange={(html) => onChange({ html })}
      />
    );
  }
  if (block.type === "image") return <ImageField data={block.data} onChange={onChange} />;
  return (
    <div className="flex flex-col gap-1 text-sm">
      <p>{definition?.description}</p>
      <p className="text-muted-foreground">Aktuell: {definition?.status}</p>
    </div>
  );
}

export function BlockCard({
  block,
  index,
  total,
  definition,
  onChange,
  onMove,
  onRemove,
}: BlockCardProps) {
  const label = definition?.label ?? block.type;
  return (
    <Card size="sm">
      <CardHeader className="flex items-center justify-between gap-2">
        <CardTitle asChild>
          <h3>
            {index + 1}. {label}
          </h3>
        </CardTitle>
        <div className="flex gap-1">
          <Button
            variant="ghost"
            size="icon-sm"
            aria-label={`${label} nach oben`}
            disabled={index === 0}
            onClick={() => onMove(-1)}
          >
            <IconArrowUp aria-hidden />
          </Button>
          <Button
            variant="ghost"
            size="icon-sm"
            aria-label={`${label} nach unten`}
            disabled={index === total - 1}
            onClick={() => onMove(1)}
          >
            <IconArrowDown aria-hidden />
          </Button>
          <Button
            variant="ghost"
            size="icon-sm"
            aria-label={`${label} entfernen`}
            onClick={onRemove}
          >
            <IconTrash aria-hidden />
          </Button>
        </div>
      </CardHeader>
      <CardContent>
        <BlockBody block={block} definition={definition} onChange={onChange} />
      </CardContent>
    </Card>
  );
}
