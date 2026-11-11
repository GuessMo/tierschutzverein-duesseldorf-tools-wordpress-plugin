import { IconHelpCircle } from "@tabler/icons-react";
import { Button, Popover, PopoverContent, PopoverTrigger } from "@web-app-kit/frontend/ui";
import type { HelpPopoverProps } from "./common.types";

export function HelpPopover({ text }: HelpPopoverProps) {
  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button
          type="button"
          variant="ghost"
          size="icon-xs"
          aria-label="Hilfe"
          title="Hilfe"
          className="align-middle text-muted-foreground"
        >
          <IconHelpCircle />
        </Button>
      </PopoverTrigger>
      <PopoverContent align="start" className="w-80 text-sm font-normal normal-case">
        <p className="leading-relaxed">{text}</p>
      </PopoverContent>
    </Popover>
  );
}
