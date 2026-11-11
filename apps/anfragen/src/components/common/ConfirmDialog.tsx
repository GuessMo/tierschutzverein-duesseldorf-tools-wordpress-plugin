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
import type { ConfirmDialogProps } from "./common.types";

export function ConfirmDialog({ confirmation, onCancel, onConfirm }: ConfirmDialogProps) {
  return (
    <AlertDialog open={confirmation !== null} onOpenChange={(open) => !open && onCancel()}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>{confirmation?.title}</AlertDialogTitle>
          <AlertDialogDescription>{confirmation?.description}</AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Abbrechen</AlertDialogCancel>
          <AlertDialogAction
            variant={confirmation?.isDestructive ? "destructive" : "default"}
            onClick={onConfirm}
          >
            {confirmation?.confirmLabel ?? ""}
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
