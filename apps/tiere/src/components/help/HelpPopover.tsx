import { IconHelpCircle } from "@tabler/icons-react";
import { Button, Popover, PopoverContent, PopoverTrigger } from "@web-app-kit/frontend/ui";
import type { HelpPopoverProps } from "./help.types";
import { useHelpText } from "./helpContext";

function HelpLine({ line }: { line: string }) {
  const match = /^(Was|Warum|Wo):\s*(.*)$/.exec(line);
  if (!match) return <p>{line}</p>;
  return (
    <p>
      <span className="font-medium">{match[1]}:</span> {match[2]}
    </p>
  );
}

export function HelpPopover({ fieldId, label }: HelpPopoverProps) {
  const text = useHelpText(fieldId);
  if (!text) return null;
  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button type="button" variant="ghost" size="icon-xs" aria-label={`Hilfe zu ${label}`}>
          <IconHelpCircle aria-hidden />
        </Button>
      </PopoverTrigger>
      <PopoverContent align="start" className="w-80 gap-2 text-sm">
        {text.split("\n").map((line) => (
          <HelpLine key={line} line={line} />
        ))}
      </PopoverContent>
    </Popover>
  );
}
