import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@web-app-kit/frontend/ui";

interface SendConfirmProps {
  isOpen: boolean;
  count: number;
  isResend: boolean;
  onCancel: () => void;
  onConfirm: () => void;
}

export function SendConfirm({ isOpen, count, isResend, onCancel, onConfirm }: SendConfirmProps) {
  return (
    <AlertDialog open={isOpen} onOpenChange={(open) => !open && onCancel()}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Newsletter an {count} Abonnenten senden?</AlertDialogTitle>
          <AlertDialogDescription>
            {isResend
              ? "Dieser Newsletter wurde schon verschickt. Alle bekommen ihn noch einmal."
              : "Die Mail geht sofort raus und lässt sich nicht zurückholen."}
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Abbrechen</AlertDialogCancel>
          <AlertDialogAction onClick={onConfirm}>Jetzt senden</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
