import { IconArrowLeft, IconExternalLink } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import { LIST_HASH } from "@/hooks";
import type { AnimalRecord } from "@/types";
import type { EditorHeaderProps } from "./editor.types";

function RecordLinks({ record }: { record: AnimalRecord }) {
  const pageUrl = record.permalink || record.preview_url;
  return (
    <div className="flex flex-wrap gap-2">
      {pageUrl && (
        <Button asChild variant="outline" size="sm">
          <a href={pageUrl} target="_blank" rel="noreferrer">
            <IconExternalLink data-icon="inline-start" />
            {record.permalink ? "Auf der Website ansehen" : "Vorschau"}
          </a>
        </Button>
      )}
      <Button asChild variant="ghost" size="sm">
        <a href={record.legacy_edit_url} target="_blank" rel="noreferrer">
          Im alten Editor öffnen
        </a>
      </Button>
    </div>
  );
}

export function EditorHeader({ name, record }: EditorHeaderProps) {
  const title = record ? name.trim() || "Ohne Namen" : "Neues Tier";
  return (
    <header className="flex flex-col gap-4">
      <Button asChild variant="ghost" size="sm" className="self-start">
        <a href={LIST_HASH}>
          <IconArrowLeft data-icon="inline-start" />
          Alle Tiere
        </a>
      </Button>
      <div className="flex flex-col gap-1">
        <h1 className="text-xl font-semibold">{title}</h1>
        {record?.internal_id && (
          <p className="text-sm text-muted-foreground tabular-nums">
            Tier-ID {record.internal_id}
          </p>
        )}
      </div>
      {record && <RecordLinks record={record} />}
    </header>
  );
}
