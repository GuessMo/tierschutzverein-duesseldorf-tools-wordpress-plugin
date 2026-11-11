import { IconDots } from "@tabler/icons-react";
import {
  Button,
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@web-app-kit/frontend/ui";
import type { CatalogEntry } from "../catalog.types";

function CardStatic() {
  return (
    <Card className="max-w-sm">
      <CardHeader>
        <CardTitle>Projekt Herbst</CardTitle>
        <CardDescription>12 Entwürfe, zuletzt geändert heute</CardDescription>
        <CardAction>
          <Button variant="ghost" size="icon-sm" aria-label="Weitere Aktionen">
            <IconDots aria-hidden />
          </Button>
        </CardAction>
      </CardHeader>
      <CardContent>Inhalt der Karte mit kurzem Fließtext.</CardContent>
      <CardFooter className="gap-2">
        <Button size="sm">Öffnen</Button>
        <Button size="sm" variant="outline">
          Teilen
        </Button>
      </CardFooter>
    </Card>
  );
}

function CardInteractive() {
  return (
    <Card interactive asChild className="max-w-sm">
      <a href="#karte">
        <CardHeader>
          <CardTitle>Ganze Karte als Link</CardTitle>
          <CardDescription>Hover, Gedrückt und Fokus auf der ganzen Fläche</CardDescription>
        </CardHeader>
      </a>
    </Card>
  );
}

export const cardEntry: CatalogEntry = {
  slug: "card",
  name: "Card",
  group: "actions",
  status: "stable",
  description:
    "Fasst zusammengehörige Inhalte auf einer Fläche zusammen. Karten werden nie verschachtelt.",
  exports: [
    "Card",
    "CardHeader",
    "CardTitle",
    "CardDescription",
    "CardAction",
    "CardContent",
    "CardFooter",
  ],
  types: ["CardProps", "CardSectionProps"],
  variants: [
    {
      key: "static",
      title: "Statisch mit Aktionen",
      description: "Kopf mit Titel, Beschreibung und CardAction, Fußzeile mit Buttons.",
      Preview: CardStatic,
    },
    {
      key: "interactive",
      title: "Interaktiv",
      description: "interactive nur mit asChild auf <a> oder <button>, ohne innere Buttons.",
      Preview: CardInteractive,
    },
  ],
  states: [
    { name: "Statisch", description: "Keine Zustände, Buttons darin bringen ihre eigenen mit." },
    { name: "Interaktiv", description: "Hover-Fläche, Gedrückt-Versatz, Fokusring." },
  ],
};
