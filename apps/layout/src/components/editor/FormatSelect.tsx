import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@web-app-kit/frontend/ui";
import { FORMAT_GROUP_LABELS, FORMATS } from "@/config/formats";
import { useEditorStore } from "@/store/editorStore";
import type { FormatGroup } from "@/types/catalog";

const GROUPS: FormatGroup[] = ["print", "social", "office"];

export function FormatSelect() {
  const formatId = useEditorStore((state) => state.document?.formatId ?? "");
  const apply = useEditorStore((state) => state.apply);

  return (
    <Select
      value={formatId}
      onValueChange={(value) => apply((document) => ({ ...document, formatId: value }))}
    >
      <SelectTrigger className="w-56" aria-label="Format">
        <SelectValue />
      </SelectTrigger>
      <SelectContent>
        {GROUPS.map((group) => (
          <SelectGroup key={group}>
            <SelectLabel>{FORMAT_GROUP_LABELS[group]}</SelectLabel>
            {FORMATS.filter((format) => format.group === group).map((format) => (
              <SelectItem key={format.id} value={format.id} disabled={!format.isAvailable}>
                {format.label}
              </SelectItem>
            ))}
          </SelectGroup>
        ))}
      </SelectContent>
    </Select>
  );
}
