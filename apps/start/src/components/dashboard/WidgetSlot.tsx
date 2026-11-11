const SLOT_CLASSES =
  "grid min-h-40 place-items-center rounded-xl border-2 border-dashed border-border p-4 text-sm text-muted-foreground";

export function WidgetSlot() {
  return <div className={SLOT_CLASSES}>Platz für ein Widget</div>;
}
