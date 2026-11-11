import type { Confirmation, ConversationAction } from "@/types";

export const CONFIRMATIONS: Partial<Record<ConversationAction | "delete", Confirmation>> = {
  block: {
    title: "Absender blockieren?",
    description: [
      "Weitere Anfragen dieser E-Mail-Adresse werden automatisch blockiert.",
      "Die Person wird nicht benachrichtigt.",
    ].join(" "),
    confirmLabel: "Blockieren",
    isDestructive: true,
  },
  unblock: {
    title: "Blockierung aufheben?",
    description: "Die Anfrage wird wieder als offen markiert.",
    confirmLabel: "Blockierung aufheben",
    isDestructive: false,
  },
  spam: {
    title: "Als Spam markieren?",
    description: "Die Anfrage verschwindet aus der Liste und liegt danach im Ordner Spam.",
    confirmLabel: "Als Spam markieren",
    isDestructive: true,
  },
  trash: {
    title: "In den Papierkorb verschieben?",
    description: "Im Papierkorb kannst du die Anfrage wiederherstellen.",
    confirmLabel: "In den Papierkorb",
    isDestructive: true,
  },
  delete: {
    title: "Anfrage endgültig löschen?",
    description: [
      "Die Anfrage und alle Nachrichten werden gelöscht.",
      "Das kann nicht rückgängig gemacht werden.",
    ].join(" "),
    confirmLabel: "Endgültig löschen",
    isDestructive: true,
  },
};
