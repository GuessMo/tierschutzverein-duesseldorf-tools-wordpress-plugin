import type { ToneDefinition } from "@/types/catalog";
import type { ToneId } from "@/types/document";

const BROWN_DARK = "#291E1B";
const BEIGE = "#F5EDE5";
const GREEN = "oklch(54.5% 0.118 172)";
const GREEN_DARK = "oklch(39.3% 0.118 172)";
const GREEN_LIGHT = "oklch(81.7% 0.118 172)";

export const TONES: ToneDefinition[] = [
  {
    id: "white",
    label: "Weiß",
    background: "#ffffff",
    foreground: BROWN_DARK,
    accent: GREEN_DARK,
  },
  { id: "beige", label: "Beige", background: BEIGE, foreground: BROWN_DARK, accent: GREEN_DARK },
  { id: "sand", label: "Sand", background: "#EBDFD4", foreground: BROWN_DARK, accent: GREEN_DARK },
  { id: "green", label: "Grün", background: GREEN, foreground: "#ffffff", accent: BROWN_DARK },
  {
    id: "greenDark",
    label: "Dunkelgrün",
    background: GREEN_DARK,
    foreground: "#ffffff",
    accent: GREEN_LIGHT,
  },
  { id: "brown", label: "Braun", background: BROWN_DARK, foreground: BEIGE, accent: GREEN_LIGHT },
  { id: "pink", label: "Rosa", background: "#F090A5", foreground: BROWN_DARK, accent: BROWN_DARK },
  {
    id: "transparent",
    label: "Ohne",
    background: "transparent",
    foreground: "inherit",
    accent: "inherit",
  },
];

export function findTone(toneId: ToneId): ToneDefinition {
  return TONES.find((tone) => tone.id === toneId) ?? TONES[0];
}
