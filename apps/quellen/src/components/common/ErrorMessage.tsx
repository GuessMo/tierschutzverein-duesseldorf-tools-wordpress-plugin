import { Button } from "@web-app-kit/frontend/ui";
import { StateMessage } from "./StateMessage";

export function ErrorMessage({ message, onRetry }: { message: string; onRetry: () => void }) {
  return (
    <div role="alert">
      <StateMessage
        title="Laden fehlgeschlagen"
        description={message}
        action={
          <Button variant="outline" onClick={onRetry}>
            Erneut versuchen
          </Button>
        }
      />
    </div>
  );
}
