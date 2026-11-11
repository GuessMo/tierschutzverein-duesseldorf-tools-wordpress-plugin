import { Skeleton } from "@web-app-kit/frontend/ui";

export function LoadingRows({ count = 4, label }: { count?: number; label: string }) {
  return (
    <div role="status" aria-label={label} className="flex flex-col gap-3">
      {Array.from({ length: count }, (_, index) => (
        <Skeleton key={index} className="h-14 w-full" />
      ))}
    </div>
  );
}
