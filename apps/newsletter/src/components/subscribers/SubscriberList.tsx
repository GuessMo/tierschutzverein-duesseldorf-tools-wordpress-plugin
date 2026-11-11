import { Checkbox, Label } from "@web-app-kit/frontend/ui";
import type { Subscriber } from "@/types";

interface SubscriberListProps {
  people: Subscriber[];
  selected: Set<number>;
  onToggle: (id: number, isOn: boolean) => void;
}

export function SubscriberList({ people, selected, onToggle }: SubscriberListProps) {
  return (
    <ul className="flex flex-col divide-y rounded-xl border">
      {people.map((person) => (
        <li key={person.id} className="flex items-center gap-3 px-4 py-3">
          <Checkbox
            id={`subscriber-${person.id}`}
            checked={selected.has(person.id)}
            onCheckedChange={(next) => onToggle(person.id, next === true)}
          />
          <Label
            htmlFor={`subscriber-${person.id}`}
            className="flex min-w-0 flex-1 flex-col items-start gap-0.5"
          >
            <span className="truncate font-medium">{person.name}</span>
            <span className="truncate text-xs font-normal text-muted-foreground">
              {person.email}
            </span>
          </Label>
        </li>
      ))}
    </ul>
  );
}
