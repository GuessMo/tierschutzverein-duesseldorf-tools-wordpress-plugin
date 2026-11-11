import type { DocumentTemplate } from "@/types/catalog";
import type { Cell } from "@/types/document";
import { cell, contactFooter, el, headerBar, photo, section } from "./builders";
import { APPOINTMENT, PHOTOS, SUPPLIES, WEBSITE } from "./content";

function animalCard(src: string, name: string): Cell {
  return cell([photo(src), el("headline", name, { size: "s" })], { tone: "white" });
}

export const POSTER_TEMPLATES: DocumentTemplate[] = [
  {
    id: "a4-donations",
    label: "Sachspenden gesucht",
    formatId: "a4",
    tone: "sand",
    createSections: () => [
      headerBar("Sachspenden"),
      section(
        "full",
        [cell([el("headline", "Das Tierheim braucht deine Sachspende", { size: "l" })])],
        { tone: "transparent", padding: "l" },
      ),
      section(
        "twoOne",
        [
          cell(
            [
              el("text", SUPPLIES, { size: "s" }),
              el("button", `Liste auf ${WEBSITE}`, { tone: "green", size: "s" }),
            ],
            { verticalAlign: "center" },
          ),
          cell([photo(PHOTOS.junis)]),
        ],
        { inset: true, tone: "beige", padding: "l", height: "fill" },
      ),
      contactFooter(APPOINTMENT),
    ],
  },
  {
    id: "a4-animals",
    label: "Diese Tiere suchen ein Zuhause",
    formatId: "a4",
    tone: "beige",
    createSections: () => [
      section(
        "full",
        [
          cell([
            el("badge", "Vereinsvermittlung", { tone: "pink" }),
            el("headline", "Diese Tiere suchen ein neues Zuhause", { size: "m" }),
          ]),
        ],
        { tone: "transparent", padding: "m" },
      ),
      section("half", [animalCard(PHOTOS.spike, "Spike"), animalCard(PHOTOS.hansel, "Hansel")], {
        tone: "transparent",
        padding: "s",
        gap: "m",
        height: "fill",
      }),
      section(
        "half",
        [animalCard(PHOTOS.muesli, "Müsli"), animalCard(PHOTOS.archibald, "Archibald")],
        { tone: "transparent", padding: "s", gap: "m", height: "fill" },
      ),
      contactFooter(),
    ],
  },
  {
    id: "din-donate",
    label: "Geldspenden",
    formatId: "din-lang",
    tone: "white",
    createSections: () => [
      section("full", [cell([photo(PHOTOS.bolle)])], { padding: "none", height: "fill" }),
      section(
        "full",
        [
          cell([
            el("headline", "Ein Tierheim kostet Geld", { size: "l" }),
            el(
              "text",
              "Spenden bezahlen Tierarztrechnungen, Löhne und Strom. Kater " +
                "Bolle bekam bei uns eine Zahnsanierung und spezielles " +
                "Futter.",
              {
                size: "m",
              },
            ),
            el(
              "text",
              "Spendenbescheinigung ab 300 € automatisch. Darunter reicht dein Kontoauszug.",
              {
                size: "s",
              },
            ),
          ]),
        ],
        { tone: "green", padding: "l" },
      ),
      contactFooter(),
    ],
  },
  {
    id: "din-food",
    label: "Futterpatenschaft",
    formatId: "din-lang",
    tone: "beige",
    createSections: () => [
      section(
        "full",
        [
          cell([
            el("badge", "Futterpatenschaft", { tone: "pink" }),
            el("headline", "Eine große Portion Glück am Tag", { size: "l", tone: "greenDark" }),
            el(
              "text",
              "Für viele Tiere im Tierheim ist die Mahlzeit das Highlight " +
                "des Tages. Mit einer Futterpatenschaft hältst du die Näpfe " +
                "voll.",
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
          cell([photo(PHOTOS.junis, { imageShape: "polaroid", align: "center" })], {
            verticalAlign: "center",
          }),
        ],
        {
          tone: "transparent",
          padding: "m",
          height: "fill",
        },
      ),
      contactFooter(),
    ],
  },
];
