import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@web-app-kit/frontend/ui";
import type { ChartCardProps } from "./charts.types";

export function ChartCard({ title, description, action, children, className }: ChartCardProps) {
  return (
    <Card className={className}>
      <CardHeader>
        <CardTitle asChild>
          <h2>{title}</h2>
        </CardTitle>
        {description && <CardDescription>{description}</CardDescription>}
        {action && <CardAction>{action}</CardAction>}
      </CardHeader>
      <CardContent>{children}</CardContent>
    </Card>
  );
}
