import { useRef } from "react";
import { IconUpload } from "@tabler/icons-react";
import { Button } from "@web-app-kit/frontend/ui";
import type { UploadButtonProps } from "./media.types";

const ACCEPT = "image/jpeg,image/png,image/webp,image/gif";

export function UploadButton({ id, label, multiple, disabled, onFiles }: UploadButtonProps) {
  const input = useRef<HTMLInputElement>(null);
  return (
    <>
      <input
        ref={input}
        id={id}
        type="file"
        accept={ACCEPT}
        multiple={multiple}
        tabIndex={-1}
        aria-hidden
        className="sr-only"
        onChange={(event) => {
          onFiles(Array.from(event.target.files ?? []));
          event.target.value = "";
        }}
      />
      <Button
        type="button"
        variant="outline"
        size="sm"
        disabled={disabled}
        onClick={() => input.current?.click()}
      >
        <IconUpload data-icon="inline-start" />
        {label}
      </Button>
    </>
  );
}
