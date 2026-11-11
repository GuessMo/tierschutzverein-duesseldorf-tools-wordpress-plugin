import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@web-app-kit/frontend/ui";

interface PreviewDialogProps {
  html: string | null;
  onClose: () => void;
}

export function PreviewDialog({ html, onClose }: PreviewDialogProps) {
  return (
    <Dialog open={html !== null} onOpenChange={(open) => !open && onClose()}>
      <DialogContent
        closeLabel="Vorschau schließen"
        className="flex h-[85vh] flex-col sm:max-w-3xl"
      >
        <DialogHeader>
          <DialogTitle>Vorschau der Mail</DialogTitle>
        </DialogHeader>
        <iframe
          title="Vorschau der Mail"
          srcDoc={html ?? ""}
          sandbox=""
          className="min-h-0 flex-1 rounded-lg border bg-white"
        />
      </DialogContent>
    </Dialog>
  );
}
