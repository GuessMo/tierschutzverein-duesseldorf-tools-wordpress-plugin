import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@web-app-kit/frontend/ui";
import type { HouseFilterProps } from "./list.types";

const ALL = "0";

export function HouseFilter({ value, houses, onChange }: HouseFilterProps) {
  return (
    <Select value={String(value)} onValueChange={(next) => onChange(Number(next))}>
      <SelectTrigger aria-label="Tierart" className="w-full">
        <SelectValue />
      </SelectTrigger>
      <SelectContent>
        <SelectGroup>
          <SelectItem value={ALL}>Alle Tierarten</SelectItem>
          {houses.map((house) => (
            <SelectItem key={house.id} value={String(house.id)}>
              {house.name}
            </SelectItem>
          ))}
        </SelectGroup>
      </SelectContent>
    </Select>
  );
}
