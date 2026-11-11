import { previewPath } from "../routing/paths";
import type { PreviewFrameProps } from "./preview.types";
import { useFrameHeight } from "./use-frame-height";

const DEFAULT_MIN_HEIGHT = "6rem";

export function PreviewFrame({ previewId, theme, title, minHeight }: PreviewFrameProps) {
  const src = previewPath(previewId, theme);
  const { frameRef, height } = useFrameHeight(src);
  return (
    <iframe
      ref={frameRef}
      src={src}
      title={title}
      className="block w-full rounded-md border"
      style={{ height: height ?? undefined, minHeight: minHeight ?? DEFAULT_MIN_HEIGHT }}
    />
  );
}
