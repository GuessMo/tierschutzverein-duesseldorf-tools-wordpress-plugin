interface LegendItem {
  label: string;
  color: string;
  value?: string;
}

export function ChartLegend({ items }: { items: LegendItem[] }) {
  return (
    <ul
      aria-label="Legende"
      className="flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-foreground"
    >
      {items.map((item) => (
        <li key={item.label} className="flex items-center gap-1.5">
          <span aria-hidden className="size-2.5 rounded-full" style={{ background: item.color }} />
          <span>{item.label}</span>
          {item.value && <span className="tabular-nums text-foreground">{item.value}</span>}
        </li>
      ))}
    </ul>
  );
}
