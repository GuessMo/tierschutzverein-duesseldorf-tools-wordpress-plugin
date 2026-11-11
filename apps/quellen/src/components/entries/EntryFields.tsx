import { TextField } from "@/components/common";
import { BodyField } from "./BodyField";
import type { DraftFieldsProps } from "./entries.types";
import { EntryMetaFields } from "./EntryMetaFields";

export function EntryFields({ draft, onChange }: DraftFieldsProps) {
  return (
    <>
      <TextField
        id="entry-title"
        label="Titel"
        value={draft.title}
        required
        maxLength={255}
        autoFocus
        onValueChange={(title) => onChange({ title })}
      />
      <TextField
        id="entry-url"
        label="Link (optional)"
        type="url"
        value={draft.url}
        placeholder="https://"
        onValueChange={(url) => onChange({ url })}
      />
      <EntryMetaFields draft={draft} onChange={onChange} />
      <BodyField value={draft.body} onChange={(body) => onChange({ body })} />
    </>
  );
}
