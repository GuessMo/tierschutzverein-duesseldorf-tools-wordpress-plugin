import { resolvePreview } from "./preview-registry";

export function PreviewApp({ previewId }: { previewId: string }) {
  const Preview = resolvePreview(previewId);
  return (
    <div className="bg-background p-4 text-foreground">
      {Preview ? <Preview /> : <p role="alert">Vorschau „{previewId}“ gibt es nicht.</p>}
    </div>
  );
}
