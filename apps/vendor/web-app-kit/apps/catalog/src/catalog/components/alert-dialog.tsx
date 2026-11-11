import { IconTrash } from "@tabler/icons-react";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogMedia,
  AlertDialogTitle,
  AlertDialogTrigger,
  Button,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewRow } from "../preview-layout";

function AlertDialogConfirm() {
  return (
    <AlertDialog>
      <AlertDialogTrigger asChild>
        <Button variant="outline">Änderungen verwerfen …</Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Änderungen verwerfen?</AlertDialogTitle>
          <AlertDialogDescription>
            Nicht gespeicherte Eingaben gehen verloren.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Weiter bearbeiten</AlertDialogCancel>
          <AlertDialogAction>Verwerfen</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}

function AlertDialogDestructive() {
  return (
    <AlertDialog>
      <AlertDialogTrigger asChild>
        <Button variant="destructive">Projekt löschen …</Button>
      </AlertDialogTrigger>
      <AlertDialogContent size="sm">
        <AlertDialogHeader>
          <AlertDialogMedia>
            <IconTrash aria-hidden />
          </AlertDialogMedia>
          <AlertDialogTitle>Projekt löschen?</AlertDialogTitle>
          <AlertDialogDescription>
            Alle 12 Entwürfe werden endgültig entfernt.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Behalten</AlertDialogCancel>
          <AlertDialogAction variant="destructive">Löschen</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}

function AlertDialogVariants() {
  return (
    <PreviewRow>
      <AlertDialogConfirm />
      <AlertDialogDestructive />
    </PreviewRow>
  );
}

export const alertDialogEntry: CatalogEntry = {
  slug: "alert-dialog",
  name: "AlertDialog",
  group: "overlays",
  status: "stable",
  description:
    "Bestätigung vor einer folgenreichen Aktion. Schließt nicht per Klick daneben; Escape " +
    "bricht ab, der Fokus startet auf Abbrechen und kehrt zum Auslöser zurück.",
  exports: [
    "AlertDialog",
    "AlertDialogTrigger",
    "AlertDialogContent",
    "AlertDialogHeader",
    "AlertDialogMedia",
    "AlertDialogTitle",
    "AlertDialogDescription",
    "AlertDialogFooter",
    "AlertDialogCancel",
    "AlertDialogAction",
    "AlertDialogOverlay",
    "AlertDialogPortal",
  ],
  variants: [
    {
      key: "default",
      title: "Bestätigung und destruktiv (size sm mit Media)",
      description:
        "AlertDialogCancel und AlertDialogAction verlangen ihren Text als children; " +
        'destruktiv mit variant="destructive" und konkretem Verb.',
      Preview: AlertDialogVariants,
    },
  ],
  states: [
    { name: "Geschlossen", description: "Nur der Auslöser ist sichtbar." },
    { name: "Offen", description: "role alertdialog, Overlay sperrt die Seite." },
  ],
  previewMinHeight: "22rem",
};
