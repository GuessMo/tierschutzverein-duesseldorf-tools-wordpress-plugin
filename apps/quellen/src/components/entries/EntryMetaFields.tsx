import { TextField } from "@/components/common";
import type { DraftFieldsProps } from "./entries.types";
import { KindSelect } from "./KindSelect";

export function EntryMetaFields({ draft, onChange }: DraftFieldsProps) {
  return (
    <div className="grid gap-4 sm:grid-cols-2">
      <TextField
        id="entry-publisher"
        label="Herausgeber"
        value={draft.publisher}
        maxLength={190}
        onValueChange={(publisher) => onChange({ publisher })}
      />
      <TextField
        id="entry-year"
        label="Jahr"
        type="number"
        min={1000}
        max={2100}
        value={draft.year}
        onValueChange={(year) => onChange({ year })}
      />
      <KindSelect value={draft.kind} onChange={(kind) => onChange({ kind })} />
      <TextField
        id="entry-accessed"
        label="Abgerufen am"
        type="date"
        value={draft.accessed_at}
        onValueChange={(accessed_at) => onChange({ accessed_at })}
      />
    </div>
  );
}
