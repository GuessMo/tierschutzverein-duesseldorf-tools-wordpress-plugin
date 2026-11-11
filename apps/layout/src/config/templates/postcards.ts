import type { DocumentTemplate } from "@/types/catalog";
import { cell, el, photo, section } from "./builders";
import { CONTACT, PHOTOS, WEBSITE } from "./content";

export const POSTCARD_TEMPLATES: DocumentTemplate[] = [
  {
    id: "a6-thanks",
    label: "Dankeskarte quer",
    formatId: "a6",
    orientation: "landscape",
    tone: "beige",
    createSections: () => [
      section(
        "half",
        [
          cell([photo(PHOTOS.candy)]),
          cell(
            [
              el("headline", "Danke!", { size: "xl", tone: "greenDark" }),
              el(
                "text",
                "Mit deiner Hilfe bekommen die Tiere im Tierheim Futter, Pflege und Zeit.",
                {
                  size: "m",
                },
              ),
            ],
            { verticalAlign: "center" },
          ),
        ],
        { tone: "transparent", padding: "m", height: "fill" },
      ),
    ],
    createBackSections: () => [
      section(
        "twoOne",
        [
          cell(
            [
              el("contact", CONTACT, { size: "m" }),
              el("text", WEBSITE, { size: "m", tone: "greenDark" }),
            ],
            {
              verticalAlign: "center",
            },
          ),
          cell([el("logo", "", { size: "l", align: "end" })], { verticalAlign: "center" }),
        ],
        { tone: "transparent", padding: "l", height: "fill" },
      ),
    ],
  },
  {
    id: "a6-volunteer",
    label: "Ehrenamt",
    formatId: "a6",
    tone: "green",
    createSections: () => [
      section(
        "full",
        [
          cell([
            el("badge", "Ehrenamt", { tone: "pink" }),
            el("headline", "Hilf mit im Tierheim", { size: "l" }),
            el(
              "text",
              "Mit Hunden spazieren gehen, Katzen streicheln oder " +
                "Taubenschläge versorgen: Jede helfende Hand wird gebraucht.",
              {
                size: "m",
              },
            ),
          ]),
        ],
        { tone: "transparent", padding: "l" },
      ),
      section(
        "full",
        [
          cell([photo(PHOTOS.spike, { imageShape: "polaroid", align: "end" })], {
            verticalAlign: "end",
          }),
        ],
        {
          tone: "transparent",
          padding: "l",
          height: "fill",
        },
      ),
    ],
  },
];
