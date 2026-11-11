import { MultiViewCard, type ChartPanelSpec } from "@tsvd/tool-controls";

interface StatsCardProps {
  title: string;
  spec: ChartPanelSpec;
  className?: string;
  description?: string;
}

export function StatsCard({ title, spec, className, description }: StatsCardProps) {
  return (
    <MultiViewCard
      id={spec.id}
      title={title}
      description={description}
      className={className}
      panels={[{ value: spec.id, label: title, kind: "chart", chart: spec }]}
    />
  );
}
