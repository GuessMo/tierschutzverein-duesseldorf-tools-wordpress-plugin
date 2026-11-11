import spikePhoto from "@/assets/samples/spike.webp";
import sharkiPhoto from "@/assets/samples/sharki.webp";
import hanselPhoto from "@/assets/samples/hansel.webp";
import junisPhoto from "@/assets/samples/junis.webp";
import candyPhoto from "@/assets/samples/candy.webp";
import pedroPhoto from "@/assets/samples/pedro.webp";
import muesliPhoto from "@/assets/samples/muesli.webp";
import piniPhoto from "@/assets/samples/pini.webp";
import archibaldPhoto from "@/assets/samples/archibald.webp";
import bollePhoto from "@/assets/samples/bolle.webp";

export const PHOTOS = {
  spike: spikePhoto,
  sharki: sharkiPhoto,
  hansel: hanselPhoto,
  junis: junisPhoto,
  candy: candyPhoto,
  pedro: pedroPhoto,
  muesli: muesliPhoto,
  pini: piniPhoto,
  archibald: archibaldPhoto,
  bolle: bollePhoto,
} as const;

export const ORGANISATION = "Tierschutzverein Düsseldorf und Umgebung e. V. 1873";

export const WEBSITE = "tierheim-duesseldorf.de";

export const CONTACT = [
  "Tierheim Düsseldorf",
  "Rüdigerstr. 1 · 40472 Düsseldorf",
  "0211 651850 · info@tierheim-duesseldorf.de",
].join("\n");

export const APPOINTMENT = "Besuche und Tiervermittlung nur nach telefonischer Terminvereinbarung.";

export const SUPPLIES = [
  "Futter für Hunde, Katzen und Kleintiere",
  "Spielzeug",
  "Reinigungsutensilien",
  "Kaffeebohnen für das Team",
].join("\n");

export const PINI_STORY =
  "Die Shiba-Inu-Hündin kam völlig verschüchtert zu uns. Am 19. " +
  "Dezember 2025 wurde sie vermittelt.";

export const SHARKI_PROFILE = [
  "Mischling, etwa 4 Jahre, rund 41 kg. Sharki wurde ausgesetzt, seine " +
    "Vorgeschichte kennen wir nicht.",
  "Trotz seiner Größe ist er sehr sensibel und braucht eine enge Bindung zu seiner Bezugsperson.",
  "Draußen ist er unkompliziert und liebt Spaziergänge.",
].join(" ");
