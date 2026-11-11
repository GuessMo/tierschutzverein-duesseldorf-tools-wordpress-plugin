import { CONTACT } from "@/config/templates/content";
import type { ElementDefinition } from "@/types/catalog";
import type { ElementKind, LayoutElement } from "@/types/document";

export const ELEMENT_DEFINITIONS: ElementDefinition[] = [
  {
    kind: "headline",
    label: "Überschrift",
    create: () => ({
      kind: "headline",
      text: "Überschrift",
      size: "l",
      tone: "transparent",
      align: "start",
    }),
  },
  {
    kind: "text",
    label: "Text",
    create: () => ({
      kind: "text",
      text: "Dein Text",
      size: "m",
      tone: "transparent",
      align: "start",
    }),
  },
  {
    kind: "image",
    label: "Bild",
    create: () => ({
      kind: "image",
      text: "",
      size: "m",
      tone: "sand",
      align: "center",
      imageShape: "landscape",
    }),
  },
  {
    kind: "logo",
    label: "Logo",
    create: () => ({
      kind: "logo",
      text: "Tierschutzverein Düsseldorf",
      size: "m",
      tone: "transparent",
      align: "start",
    }),
  },
  {
    kind: "button",
    label: "Button",
    create: () => ({
      kind: "button",
      text: "Jetzt mitmachen",
      size: "m",
      tone: "green",
      align: "start",
    }),
  },
  {
    kind: "contact",
    label: "Kontakt",
    create: () => ({
      kind: "contact",
      text: CONTACT,
      size: "s",
      tone: "transparent",
      align: "start",
    }),
  },
  {
    kind: "badge",
    label: "Störer",
    create: () => ({ kind: "badge", text: "Neu", size: "m", tone: "pink", align: "start" }),
  },
  {
    kind: "divider",
    label: "Linie",
    create: () => ({ kind: "divider", text: "", size: "s", tone: "transparent", align: "start" }),
  },
  {
    kind: "spacer",
    label: "Abstand",
    create: () => ({ kind: "spacer", text: "", size: "m", tone: "transparent", align: "start" }),
  },
];

export function findElementDefinition(kind: ElementKind): ElementDefinition {
  const definition = ELEMENT_DEFINITIONS.find((entry) => entry.kind === kind);
  if (!definition) throw new RangeError(`Unbekannter Elementtyp: ${kind}`);
  return definition;
}

export function hasEditableText(element: LayoutElement): boolean {
  return !["image", "divider", "spacer"].includes(element.kind);
}
