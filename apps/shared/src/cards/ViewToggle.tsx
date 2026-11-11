import { ToggleGroup, ToggleGroupItem } from "@web-app-kit/frontend/ui";

export interface ViewOption {
  value: string;
  label: string;
}

interface ViewToggleProps {
  label: string;
  options: ViewOption[];
  value: string;
  onChange: (value: string) => void;
}

export function ViewToggle({ label, options, value, onChange }: ViewToggleProps) {
  return (
    <ToggleGroup
      type="single"
      variant="outline"
      size="sm"
      label={label}
      value={value}
      onValueChange={(next) => next && onChange(next)}
      className="flex-wrap"
    >
      {options.map((option) => (
        <ToggleGroupItem key={option.value} value={option.value}>
          {option.label}
        </ToggleGroupItem>
      ))}
    </ToggleGroup>
  );
}
