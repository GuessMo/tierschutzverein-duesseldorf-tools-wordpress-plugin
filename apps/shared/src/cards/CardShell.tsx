import type { ReactNode } from "react";
import { SINGLE } from "./layout";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@web-app-kit/frontend/ui";

interface CardShellProps {
  title: string;
  description?: string;
  views?: ReactNode;
  className?: string;
  children: ReactNode;
}

export function CardShell({ title, description, views, className, children }: CardShellProps) {
  return (
    <Card className={className || SINGLE}>
      <CardHeader className="flex flex-wrap items-start justify-between gap-3">
        <div className="flex flex-col gap-1">
          <CardTitle asChild>
            <h2>{title}</h2>
          </CardTitle>
          {description && <CardDescription>{description}</CardDescription>}
        </div>
        {views}
      </CardHeader>
      {children}
    </Card>
  );
}

export function CardBody({ children, footer }: { children: ReactNode; footer?: ReactNode }) {
  return (
    <>
      <CardContent className="flex-1">{children}</CardContent>
      {footer && <CardFooter className="py-2">{footer}</CardFooter>}
    </>
  );
}
