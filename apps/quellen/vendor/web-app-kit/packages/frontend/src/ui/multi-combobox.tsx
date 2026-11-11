import * as React from "react";
import { IconSelector, IconX } from "@tabler/icons-react";

import { Badge } from "./badge.js";
import { Button } from "./button.js";
import { cn } from "./cn.js";
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "./command.js";
import type { MultiComboboxOption, MultiComboboxProps } from "./multi-combobox.types.js";
import { Popover, PopoverContent, PopoverTrigger } from "./popover.js";

const TRIGGER_CLASSES = [
  "w-full justify-between font-normal text-muted-foreground",
  "aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20",
];

const CONTENT_CLASSES = "w-(--radix-popover-trigger-width) min-w-64 p-0";

function labelOf(options: readonly MultiComboboxOption[], value: string): string {
  return options.find((option) => option.value === value)?.label ?? value;
}

function toggled(selected: readonly string[], value: string): string[] {
  return selected.includes(value)
    ? selected.filter((entry) => entry !== value)
    : [...selected, value];
}

function MultiCombobox({
  id,
  options,
  value,
  onValueChange,
  placeholder,
  searchPlaceholder,
  emptyText,
  selectedLabel,
  removeLabel,
  disabled = false,
  className,
  ...aria
}: MultiComboboxProps) {
  const [isOpen, setIsOpen] = React.useState(false);
  return (
    <div data-slot="multi-combobox" className={cn("flex flex-col gap-2", className)}>
      <Popover open={isOpen} onOpenChange={setIsOpen}>
        <PopoverTrigger asChild>
          <Button
            id={id}
            type="button"
            variant="outline"
            role="combobox"
            aria-expanded={isOpen}
            disabled={disabled}
            className={cn(TRIGGER_CLASSES)}
            {...aria}
          >
            {placeholder}
            <IconSelector aria-hidden className="opacity-50" />
          </Button>
        </PopoverTrigger>
        <PopoverContent align="start" className={CONTENT_CLASSES}>
          <Command label={searchPlaceholder}>
            <CommandInput placeholder={searchPlaceholder} />
            <CommandList>
              <CommandEmpty>{emptyText}</CommandEmpty>
              <CommandGroup>
                {options.map((option) => {
                  const isChecked = value.includes(option.value);
                  return (
                    <CommandItem
                      key={option.value}
                      value={`${option.label} ${option.value}`}
                      data-checked={isChecked}
                      onSelect={() => onValueChange(toggled(value, option.value))}
                    >
                      {option.label}
                    </CommandItem>
                  );
                })}
              </CommandGroup>
            </CommandList>
          </Command>
        </PopoverContent>
      </Popover>
      {value.length > 0 && (
        <ul aria-label={selectedLabel} className="flex flex-wrap gap-1.5">
          {value.map((entry) => {
            const label = labelOf(options, entry);
            return (
              <li key={entry}>
                <Badge variant="outline" className="h-6 pr-0.5">
                  {label}
                  <Button
                    type="button"
                    variant="ghost"
                    size="icon-xs"
                    disabled={disabled}
                    aria-label={removeLabel(label)}
                    onClick={() => onValueChange(value.filter((item) => item !== entry))}
                  >
                    <IconX aria-hidden />
                  </Button>
                </Badge>
              </li>
            );
          })}
        </ul>
      )}
    </div>
  );
}

export { MultiCombobox };
