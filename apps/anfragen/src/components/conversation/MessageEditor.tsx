import { useState } from "react";
import { Button, Textarea } from "@web-app-kit/frontend/ui";

interface MessageEditorProps {
  initial: string;
  onCancel: () => void;
  onSave: (body: string) => Promise<boolean>;
}

export function MessageEditor({ initial, onCancel, onSave }: MessageEditorProps) {
  const [body, setBody] = useState(initial);
  return (
    <div className="flex flex-col gap-2">
      <Textarea
        aria-label="Nachricht bearbeiten"
        value={body}
        rows={4}
        onChange={(event) => setBody(event.target.value)}
      />
      <div className="flex gap-2">
        <Button size="sm" disabled={!body.trim()} onClick={() => void onSave(body)}>
          Speichern
        </Button>
        <Button size="sm" variant="ghost" onClick={onCancel}>
          Abbrechen
        </Button>
      </div>
    </div>
  );
}
