import type { DocumentTemplate } from "@/types/catalog";
import { cell, contactFooter, el, headerBar, photo, section } from "./builders";
import { APPOINTMENT, PHOTOS, SHARKI_PROFILE, WEBSITE } from "./content";

export const PRINT_TEMPLATES: DocumentTemplate[] = [
  {
    id: "a5-sharki",
    label: "Tier sucht Zuhause",
    formatId: "a5",
    tone: "sand",
    createSections: () => [
      headerBar("Vereinsvermittlung"),
      section("full", [cell([photo(PHOTOS.sharki)])], {
        inset: true,
        padding: "none",
        height: "fill",
      }),
      section(
        "full",
        [cell([el("headline", "Sharki sucht Menschen mit Ruhe und Geduld", { size: "l" })])],
        { inset: true, tone: "beige" },
      ),
    ],
    createBackSections: () => [
      section(
        "full",
        [
          cell([
            el("badge", "Steckbrief", { tone: "pink" }),
            el("headline", "Das ist Sharki", { size: "l" }),
            el("text", SHARKI_PROFILE, { size: "s" }),
          ]),
        ],
        { inset: true, tone: "beige", padding: "l", height: "fill" },
      ),
      section(
        "half",
        [
          cell([photo(PHOTOS.sharki, { imageShape: "polaroid" })], { verticalAlign: "center" }),
          cell(
            [
              el("headline", "Lerne ihn kennen", { size: "s" }),
              el("text", APPOINTMENT, { size: "s" }),
              el("button", "0211 651850", { tone: "green", size: "s" }),
            ],
            { verticalAlign: "center" },
          ),
        ],
        { tone: "transparent", padding: "m" },
      ),
      contactFooter(),
    ],
  },
  {
    id: "a5-membership",
    label: "Mitglied werden",
    formatId: "a5",
    tone: "sand",
    createSections: () => [
      section(
        "half",
        [
          cell([photo(PHOTOS.muesli, { imageShape: "polaroid" })], { verticalAlign: "center" }),
          cell(
            [
              el("badge", "Ab 36 € im Jahr", { tone: "pink" }),
              el("headline", "Werde Mitglied im Tierschutzverein", { size: "m" }),
              el("text", "Dein Beitrag hilft uns, für Tiere in Not in Düsseldorf da zu sein."),
            ],
            { verticalAlign: "center" },
          ),
        ],
        { inset: true, tone: "green", padding: "l", height: "fill" },
      ),
      section(
        "thirds",
        [
          cell(
            [
              el("headline", "36 €", { size: "m" }),
              el("text", "Mindestbeitrag im Jahr", { size: "s" }),
            ],
            {
              tone: "white",
            },
          ),
          cell(
            [
              el("headline", "Mitreden", { size: "s" }),
              el("text", "Du wählst den Verwaltungsausschuss mit.", { size: "s" }),
            ],
            { tone: "white" },
          ),
          cell(
            [
              el("headline", "Seit 1873", { size: "s" }),
              el("text", "Tierschutz in Düsseldorf und Umgebung", { size: "s" }),
            ],
            {
              tone: "white",
            },
          ),
        ],
        { inset: true, tone: "beige", gap: "s" },
      ),
      section(
        "full",
        [cell([el("button", `Online anmelden: ${WEBSITE}`, { tone: "green", align: "center" })])],
        {
          tone: "transparent",
          padding: "s",
        },
      ),
    ],
  },
];
