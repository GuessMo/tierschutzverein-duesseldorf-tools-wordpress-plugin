import { cell, contactFooter, el, headerBar, photo, section } from "@/config/templates/builders";
import { PHOTOS, WEBSITE } from "@/config/templates/content";
import type { SectionPreset } from "@/types/catalog";

export const SECTION_PRESETS: SectionPreset[] = [
  {
    id: "stage",
    label: "Bühne",
    create: () =>
      section(
        "full",
        [
          cell(
            [
              el("badge", "Zuhause gesucht", { tone: "pink" }),
              el("headline", "Spike", { size: "xl" }),
              el("text", "Deutsche Dogge · 7 Jahre · etwa 45 kg", { size: "l" }),
              el("button", `Mehr auf ${WEBSITE}`, { tone: "white" }),
            ],
            { verticalAlign: "end" },
          ),
        ],
        {
          tone: "brown",
          padding: "l",
          height: "fill",
          backgroundImage: PHOTOS.spike,
          overlay: true,
        },
      ),
  },
  {
    id: "image-text",
    label: "Bild + Text",
    create: () =>
      section(
        "half",
        [
          cell([photo(PHOTOS.hansel)]),
          cell(
            [
              el("headline", "Hansel", { size: "m" }),
              el("text", "Neugieriger Kater, 2 Jahre. Sucht ein Zuhause als Einzelkater.", {
                size: "s",
              }),
            ],
            { verticalAlign: "center" },
          ),
        ],
        { inset: true, tone: "beige" },
      ),
  },
  {
    id: "text-polaroid",
    label: "Text + Polaroid",
    create: () =>
      section(
        "twoOne",
        [
          cell(
            [
              el("headline", "Candy sucht ein Zuhause", { size: "m" }),
              el("text", "Neugierig, frisst aus der Hand und lässt sich gern streicheln.", {
                size: "s",
              }),
            ],
            { verticalAlign: "center" },
          ),
          cell([photo(PHOTOS.candy, { imageShape: "polaroid", align: "end" })], {
            verticalAlign: "center",
          }),
        ],
        { tone: "green" },
      ),
  },
  {
    id: "three-facts",
    label: "Drei Fakten",
    create: () =>
      section(
        "thirds",
        [
          ["36 €", "Mindestbeitrag im Jahr"],
          ["Mitreden", "Du wählst den Verwaltungsausschuss mit."],
          ["Seit 1873", "Tierschutz in Düsseldorf und Umgebung"],
        ].map(([title, text]) =>
          cell([el("headline", title, { size: "s" }), el("text", text, { size: "s" })], {
            tone: "white",
          }),
        ),
        { inset: true, tone: "beige", gap: "s" },
      ),
  },
  { id: "contact-footer", label: "Kontakt-Fuß", create: () => contactFooter() },
  { id: "header-bar", label: "Kopfzeile", create: () => headerBar("Vereinsvermittlung") },
];
