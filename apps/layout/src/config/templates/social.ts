import type { DocumentTemplate } from "@/types/catalog";
import { cell, el, photo, photoStage, section, websiteFooter } from "./builders";
import { PHOTOS, PINI_STORY } from "./content";

export const SOCIAL_TEMPLATES: DocumentTemplate[] = [
  {
    id: "ig-square-week",
    label: "Tier der Woche",
    formatId: "ig-square",
    createSections: () => [
      photoStage(PHOTOS.hansel, [
        el("badge", "Tier der Woche", { tone: "pink" }),
        el("headline", "Hansel", { size: "xl" }),
        el("text", "2 Jahre · neugierig · sucht ein Zuhause als Einzelkater", { size: "l" }),
      ]),
      websiteFooter(),
    ],
  },
  {
    id: "ig-square-happy-end",
    label: "Glücklich vermittelt",
    formatId: "ig-square",
    createSections: () => [
      photoStage(PHOTOS.pini, [
        el("badge", "Glücklich vermittelt", { tone: "green" }),
        el("headline", "Pini ist angekommen", { size: "l" }),
        el("text", PINI_STORY, { size: "m" }),
      ]),
      websiteFooter(),
    ],
  },
  {
    id: "ig-portrait-spike",
    label: "Zuhause gesucht",
    formatId: "ig-portrait",
    createSections: () => [
      photoStage(PHOTOS.spike, [
        el("badge", "Zuhause gesucht", { tone: "pink" }),
        el("headline", "Spike", { size: "xl" }),
        el(
          "text",
          "Deutsche Dogge · 7 Jahre · etwa 45 kg\nEin sensibler Riese, " +
            "der geduldige und erfahrene Menschen sucht.",
          {
            size: "m",
          },
        ),
      ]),
      websiteFooter(),
    ],
  },
  {
    id: "ig-portrait-pair",
    label: "Zu zweit",
    formatId: "ig-portrait",
    tone: "green",
    createSections: () => [
      section(
        "full",
        [
          cell([
            el("badge", "Vereinsvermittlung", { tone: "pink" }),
            el("headline", "Candy & Pedro suchen ein Zuhause", { size: "l" }),
            el("text", "Neugierig, fressen aus der Hand und lassen sich gern streicheln.", {
              size: "m",
            }),
          ]),
        ],
        { tone: "transparent", padding: "l" },
      ),
      section(
        "half",
        [
          cell([photo(PHOTOS.candy, { imageShape: "polaroid" })], { verticalAlign: "center" }),
          cell([photo(PHOTOS.pedro, { imageShape: "polaroid", align: "end" })], {
            verticalAlign: "center",
          }),
        ],
        { tone: "transparent", padding: "m", height: "fill" },
      ),
      websiteFooter("brown"),
    ],
  },
];
