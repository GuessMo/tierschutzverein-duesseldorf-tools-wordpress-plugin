import type { StateMessageProps } from "./common.types";

const FRAME_CLASSES = [
  "flex w-full min-w-0 flex-1 flex-col items-center justify-center gap-4",
  "rounded-xl border border-dashed p-6 text-center text-balance",
].join(" ");

export function StateMessage({ title, description, action }: StateMessageProps) {
  return (
    <div className={FRAME_CLASSES}>
      <div className="flex max-w-sm flex-col items-center gap-2">
        <p className="text-sm font-medium tracking-tight">{title}</p>
        {description && <p className="text-sm/relaxed text-muted-foreground">{description}</p>}
      </div>
      {action && (
        <div className="flex w-full max-w-sm min-w-0 flex-col items-center gap-2.5 text-sm">
          {action}
        </div>
      )}
    </div>
  );
}
