import { IconPaw } from "@tabler/icons-react";

export function AnimalThumb({ src }: { src: string }) {
  if (!src) {
    return (
      <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-muted text-muted-foreground">
        <IconPaw aria-hidden className="size-5" />
      </span>
    );
  }
  return <img src={src} alt="" className="size-10 shrink-0 rounded-md object-cover" />;
}
