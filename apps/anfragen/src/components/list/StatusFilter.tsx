import { IconDots } from "@tabler/icons-react";
import {
  Button,
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuRadioGroup,
  DropdownMenuRadioItem,
  DropdownMenuTrigger,
  ToggleGroup,
  ToggleGroupItem,
} from "@web-app-kit/frontend/ui";
import { countKey, isMoreStatus, MORE_STATUSES, PRIMARY_STATUSES } from "@/config/statuses";
import type { CountKey, ListStatus } from "@/types";
import type { StatusFilterProps } from "./list.types";

function withCount(label: string, status: ListStatus, counts: Record<CountKey, number> | null) {
  const count = counts?.[countKey(status)];
  return count === undefined ? label : `${label} ${count}`;
}

function MoreFolders({ value, counts, onChange }: StatusFilterProps) {
  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button
          variant={isMoreStatus(value) ? "secondary" : "ghost"}
          size="icon-sm"
          aria-label="Weitere Ordner"
        >
          <IconDots aria-hidden />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        <DropdownMenuRadioGroup value={value} onValueChange={(next) => onChange(next as ListStatus)}>
          {MORE_STATUSES.map((item) => (
            <DropdownMenuRadioItem key={item.value} value={item.value}>
              {withCount(item.label, item.value, counts)}
            </DropdownMenuRadioItem>
          ))}
        </DropdownMenuRadioGroup>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}

export function StatusFilter(props: StatusFilterProps) {
  const { value, counts, onChange } = props;
  return (
    <div className="flex items-center justify-between gap-2">
      <ToggleGroup
        type="single"
        variant="outline"
        size="sm"
        label="Ordner"
        value={isMoreStatus(value) ? "" : value || "all"}
        onValueChange={(next) => next && onChange(next === "all" ? "" : (next as ListStatus))}
      >
        {PRIMARY_STATUSES.map((item) => (
          <ToggleGroupItem key={item.label} value={item.value || "all"}>
            {withCount(item.label, item.value, counts)}
          </ToggleGroupItem>
        ))}
      </ToggleGroup>
      <MoreFolders {...props} />
    </div>
  );
}
