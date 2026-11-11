import type { PlotSeries } from "./charts.types";

interface SeriesTableProps {
  labels: string[];
  series: PlotSeries[];
  format: (value: number) => string;
  caption: string;
}

export function SeriesTable({ labels, series, format, caption }: SeriesTableProps) {
  return (
    <table className="sr-only">
      <caption>{caption}</caption>
      <thead>
        <tr>
          <th scope="col">Wert</th>
          {series.map((entry) => (
            <th key={entry.key} scope="col">
              {entry.label}
            </th>
          ))}
        </tr>
      </thead>
      <tbody>
        {labels.map((name, index) => (
          <tr key={name}>
            <th scope="row">{name}</th>
            {series.map((entry) => (
              <td key={entry.key}>{format(entry.values[index] ?? 0)}</td>
            ))}
          </tr>
        ))}
      </tbody>
    </table>
  );
}
