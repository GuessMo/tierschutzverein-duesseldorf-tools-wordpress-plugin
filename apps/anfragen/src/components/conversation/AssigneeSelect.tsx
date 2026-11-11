import {
  Label,
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@web-app-kit/frontend/ui";
import type { AssigneeSelectProps } from "./conversation.types";

const NOBODY = "0";

export function AssigneeSelect({ value, assignees, onChange }: AssigneeSelectProps) {
  const isKnown = assignees.some((assignee) => assignee.id === value);
  const current = isKnown ? String(value) : NOBODY;
  return (
    <div className="flex items-center gap-2">
      <Label htmlFor="anfrage-assignee" className="text-sm text-muted-foreground">
        Zuständig
      </Label>
      <Select value={current} onValueChange={(next) => onChange(Number(next))}>
        <SelectTrigger id="anfrage-assignee" size="sm" className="min-w-40">
          <SelectValue />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            <SelectItem value={NOBODY}>Niemand</SelectItem>
            {assignees.map((assignee) => (
              <SelectItem key={assignee.id} value={String(assignee.id)}>
                {assignee.label}
              </SelectItem>
            ))}
          </SelectGroup>
        </SelectContent>
      </Select>
    </div>
  );
}
