import { Button } from "@web-app-kit/frontend/ui";
import { FormError, SelectField } from "@/components/common";
import { POST_STATUS_LABELS, SAVE_STATUSES } from "@/config/labels";
import type { AnimalOptions, PostStatus, ValueLabel } from "@/types";
import type { SaveBarProps } from "./editor.types";

function statusOptions(current: PostStatus, options: AnimalOptions): ValueLabel[] {
  const allowed: PostStatus[] = SAVE_STATUSES.filter(
    (status) => status !== "publish" || options.caps.publish || current === "publish",
  );
  const values = allowed.includes(current) ? allowed : [current, ...allowed];
  return values.map((value) => ({ value, label: POST_STATUS_LABELS[value] }));
}

function TrashNotice({ isBusy, onRestore }: { isBusy: boolean; onRestore: () => void }) {
  return (
    <div className="flex flex-wrap items-center gap-3">
      <p className="text-sm">Dieses Tier liegt im Papierkorb.</p>
      <Button type="button" variant="outline" disabled={isBusy} onClick={onRestore}>
        Wiederherstellen
      </Button>
    </div>
  );
}

function SaveActions({ isBusy, isDirty }: { isBusy: boolean; isDirty: boolean }) {
  return (
    <div className="flex items-center gap-3">
      <span role="status" className="text-sm text-muted-foreground">
        {isDirty ? "Ungespeicherte Änderungen" : ""}
      </span>
      <Button type="submit" disabled={isBusy} title="Speichern (Strg/Cmd + S)">
        {isBusy ? "Speichert …" : "Speichern"}
      </Button>
    </div>
  );
}

export function SaveBar(props: SaveBarProps) {
  const { draft, options, record, isBusy, isDirty, error, onChange, onRestore } = props;
  const isTrashed = record?.status === "trash";
  return (
    <div className="sticky bottom-0 z-10 border-t bg-background">
      <div className="mx-auto flex w-full max-w-3xl flex-col gap-3 px-4 py-3 sm:px-6">
        <FormError message={error} />
        {isTrashed ? (
          <TrashNotice isBusy={isBusy} onRestore={onRestore} />
        ) : (
          <div className="flex flex-wrap items-end justify-between gap-3">
            <div className="w-48">
              <SelectField
                id="animal-status"
                label="Sichtbarkeit"
                value={draft.status}
                options={statusOptions(record?.status ?? "draft", options)}
                onValueChange={(status) => onChange({ status: status as PostStatus })}
              />
            </div>
            <SaveActions isBusy={isBusy} isDirty={isDirty} />
          </div>
        )}
      </div>
    </div>
  );
}
