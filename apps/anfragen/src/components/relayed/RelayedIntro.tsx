import { IconInfoCircle } from "@tabler/icons-react";

const INTRO_CLASSES = [
  "flex gap-3 rounded-xl border bg-primary-subtle p-4 text-sm",
  "text-primary-subtle-foreground",
].join(" ");

const WHAT =
  "Hier landen Nachrichten, die Besucher über die Website zu privaten " +
  "Vermittlungsanzeigen oder zu Vermisst-Meldungen schicken.";

const HOW =
  "Wir antworten darauf nicht selbst: Die Website leitet jede Nachricht einmal mit den " +
  "Kontaktdaten an die Halterin oder den Halter weiter. Danach klären beide alles direkt.";

function whyText(days: number): string {
  return (
    "Die Liste dient nur zur Kontrolle, ob die Weiterleitung geklappt hat. " +
    `Einträge werden nach ${days} Tagen automatisch gelöscht.`
  );
}

export function RelayedIntro({ retentionDays }: { retentionDays: number }) {
  return (
    <section aria-label="Was ist das?" className={INTRO_CLASSES}>
      <IconInfoCircle aria-hidden className="mt-0.5 size-5 shrink-0" />
      <div className="flex max-w-prose flex-col gap-2">
        <p className="font-medium">{WHAT}</p>
        <p>{HOW}</p>
        <p>{whyText(retentionDays)}</p>
      </div>
    </section>
  );
}
