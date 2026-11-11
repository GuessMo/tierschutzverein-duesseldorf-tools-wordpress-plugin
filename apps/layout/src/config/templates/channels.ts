import type { DocumentTemplate } from "@/types/catalog";
import { cell, el, photo, photoStage, section } from "./builders";
import { PHOTOS, WEBSITE } from "./content";

export const CHANNEL_TEMPLATES: DocumentTemplate[] = [
  {
    id: "story-archibald",
    label: "Story Tierporträt",
    formatId: "story",
    createSections: () => [
      photoStage(PHOTOS.archibald, [
        el("badge", "Zuhause gesucht", { tone: "pink" }),
        el("headline", "Archibald", { size: "xl" }),
        el(
          "text",
          "Graupapagei mit ganz eigener Persönlichkeit. Wen er ins Herz " +
            "schließt, dem begegnet er anhänglich und kuschelfreudig.",
          {
            size: "l",
          },
        ),
        el("button", `Mehr auf ${WEBSITE}`, { tone: "white" }),
      ]),
    ],
  },
  {
    id: "story-volunteer",
    label: "Story Ehrenamt",
    formatId: "story",
    tone: "green",
    createSections: () => [
      section(
        "full",
        [
          cell([
            el("badge", "Ehrenamt", { tone: "pink" }),
            el("headline", "Hilf mit im Tierheim", { size: "xl" }),
            el(
              "text",
              "Mit Hunden spazieren gehen, Katzen streicheln oder Taubenschläge versorgen.",
              { size: "l" },
            ),
          ]),
        ],
        { tone: "transparent", padding: "l" },
      ),
      section(
        "full",
        [
          cell([photo(PHOTOS.sharki, { imageShape: "polaroid", align: "end" })], {
            verticalAlign: "center",
          }),
        ],
        {
          tone: "transparent",
          padding: "l",
          height: "fill",
        },
      ),
      section(
        "full",
        [cell([el("button", "Melde dich: 0211 651850", { tone: "white", align: "center" })])],
        {
          tone: "transparent",
          padding: "l",
        },
      ),
    ],
  },
  {
    id: "fb-muesli",
    label: "Link-Vorschau Tier",
    formatId: "fb-link",
    tone: "beige",
    createSections: () => [
      section(
        "half",
        [
          cell([photo(PHOTOS.muesli)]),
          cell(
            [
              el("badge", "Zuhause gesucht", { tone: "pink" }),
              el("headline", "Müsli", { size: "m" }),
              el(
                "text",
                "Staffordshire-Mischling, 7 Jahre. Fröhlich, offen zu jedem " +
                  "Menschen und fährt gern Auto.",
                { size: "s" },
              ),
              el("logo", "Tierschutzverein Düsseldorf", { size: "s" }),
            ],
            { verticalAlign: "center" },
          ),
        ],
        { tone: "transparent", padding: "s", height: "fill", gap: "m" },
      ),
    ],
  },
  {
    id: "fb-membership",
    label: "Link-Vorschau Mitglied",
    formatId: "fb-link",
    tone: "green",
    createSections: () => [
      section(
        "twoOne",
        [
          cell(
            [
              el("badge", "Ab 36 € im Jahr", { tone: "pink" }),
              el("headline", "Werde Mitglied im Tierschutzverein", { size: "s" }),
              el("text", "Seit 1873 für Tiere in Düsseldorf und Umgebung.", { size: "m" }),
            ],
            { verticalAlign: "center" },
          ),
          cell([photo(PHOTOS.bolle, { imageShape: "polaroid", align: "end" })], {
            verticalAlign: "center",
          }),
        ],
        { tone: "transparent", padding: "l", height: "fill" },
      ),
    ],
  },
];
