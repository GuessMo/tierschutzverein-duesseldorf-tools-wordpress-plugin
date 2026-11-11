import type { MouseEvent } from "react";
import { IconArrowLeft, IconExternalLink, IconMessages, IconPaw } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import { CopyValue, StateBadge } from "@/components/common";
import { LIST_HASH } from "@/hooks";
import { stateOf } from "@/lib";
import type { AnimalImage, AnimalRecord } from "@/types";
import type { EditorHeaderProps } from "./editor.types";
import { RecordTypeSwitch } from "./RecordTypeSwitch";

function HeaderImage({ image }: { image: AnimalImage | null }) {
  if (image) {
    return (
      <img src={image.large || image.thumb} alt="" className="size-28 rounded-xl object-cover" />
    );
  }
  return (
    <span className="flex size-28 items-center justify-center rounded-xl bg-muted text-muted-foreground">
      <IconPaw aria-hidden className="size-10" stroke={1.5} />
    </span>
  );
}

function inquiryLabel(count: number): string {
  if (count === 0) return "Keine Anfragen";
  return count === 1 ? "1 Anfrage" : `${count} Anfragen`;
}

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
      {record.inquiries && (
        <Button asChild variant="outline" size="sm">
          <a href={record.inquiries.url} target="_blank" rel="noopener">
            <IconMessages data-icon="inline-start" />
            {inquiryLabel(record.inquiries.count)}
          </a>
        </Button>
      )}
      <Button asChild variant="outline" size="sm">
        <a href={record.legacy_edit_url} target="_blank" rel="noreferrer">
          Im alten Editor öffnen
        </a>
      </Button>
    </div>
  );
}

const LEAVE_QUESTION = "Du hast ungespeicherte Änderungen. Trotzdem zur Liste wechseln?";

export function EditorHeader(props: EditorHeaderProps) {
  const { draft, options, record, isDirty, onChange } = props;
  const title = record ? draft.name.trim() || "Ohne Namen" : "Neues Tier";
  const confirmLeave = (event: MouseEvent<HTMLAnchorElement>) => {
    if (isDirty && !window.confirm(LEAVE_QUESTION)) event.preventDefault();
  };
  return (
    <header className="flex flex-col gap-4">
      <Button asChild variant="ghost" size="sm" className="self-start">
        <a href={LIST_HASH} onClick={confirmLeave}>
          <IconArrowLeft data-icon="inline-start" />
          Alle Tiere
        </a>
      </Button>
      <div className="flex flex-col gap-4 sm:flex-row sm:items-start">
        <HeaderImage image={draft.featured_image} />
        <div className="flex min-w-0 flex-col gap-3">
          <div className="flex flex-wrap items-center gap-3">
            <h1 className="text-2xl font-semibold">{title}</h1>
            <StateBadge status={stateOf(draft, options)} />
          </div>
          <div className="flex flex-wrap gap-x-4 gap-y-1">
            {record?.internal_id && <CopyValue label="Tier-ID" value={record.internal_id} />}
            {draft.teo_id && <CopyValue label="Teo-ID" value={draft.teo_id} />}
          </div>
          <RecordTypeSwitch
            value={draft.record_type}
            onChange={(record_type) => onChange({ record_type })}
          />
        </div>
      </div>
      {record && <RecordLinks record={record} />}
    </header>
  );
}
