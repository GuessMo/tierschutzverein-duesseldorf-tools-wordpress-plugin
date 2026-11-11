import {
  Button,
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  Input,
  Label,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";
import { PreviewRow } from "../preview-layout";

function DialogForm() {
  return (
    <Dialog>
      <DialogTrigger asChild>
        <Button>Projekt anlegen</Button>
      </DialogTrigger>
      <DialogContent closeLabel="Schließen">
        <DialogHeader>
          <DialogTitle>Neues Projekt</DialogTitle>
          <DialogDescription>Der Name ist später änderbar.</DialogDescription>
        </DialogHeader>
        <div className="flex flex-col gap-2">
          <Label htmlFor="dialog-name">Projektname</Label>
          <Input id="dialog-name" />
        </div>
        <DialogFooter>
          <DialogClose asChild>
            <Button variant="outline">Abbrechen</Button>
          </DialogClose>
          <Button>Anlegen</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

function DialogDestructive() {
  return (
    <Dialog>
      <DialogTrigger asChild>
        <Button variant="outline">Projekt löschen …</Button>
      </DialogTrigger>
      <DialogContent showCloseButton={false}>
        <DialogHeader>
          <DialogTitle>Projekt löschen?</DialogTitle>
          <DialogDescription>Alle 12 Entwürfe werden endgültig entfernt.</DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <DialogClose asChild>
            <Button variant="outline">Abbrechen</Button>
          </DialogClose>
          <Button variant="destructive">Projekt löschen</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

function DialogVariants() {
  return (
    <PreviewRow>
      <DialogForm />
      <DialogDestructive />
    </PreviewRow>
  );
}

export const dialogEntry: CatalogEntry = {
  slug: "dialog",
  name: "Dialog",
  group: "overlays",
  status: "stable",
  description:
    "Modale Aufgabe, die den Rest der Seite sperrt. DialogTitle ist Pflicht; Escape " +
    "schließt, der Fokus kehrt zum Auslöser zurück.",
  exports: [
    "Dialog",
    "DialogTrigger",
    "DialogContent",
    "DialogHeader",
    "DialogTitle",
    "DialogDescription",
    "DialogFooter",
    "DialogClose",
    "DialogOverlay",
    "DialogPortal",
  ],
  types: ["DialogContentProps", "DialogFooterProps"],
  variants: [
    {
      key: "default",
      title: "Formular und destruktive Bestätigung",
      description:
        "closeLabel ist Pflicht, solange der Schließen-Button sichtbar ist; destruktiv mit " +
        "konkretem Verb statt OK.",
      Preview: DialogVariants,
    },
  ],
  states: [
    { name: "Geschlossen", description: "Nur der Auslöser ist sichtbar." },
    { name: "Offen", description: "Overlay sperrt die Seite, Fokus liegt im Dialog." },
  ],
  previewMinHeight: "22rem",
};
