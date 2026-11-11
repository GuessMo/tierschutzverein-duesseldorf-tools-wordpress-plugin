export function EmptyNote({ text = "Keine Daten im Zeitraum." }: { text?: string }) {
  return <p className="py-6 text-sm text-muted-foreground">{text}</p>;
}
