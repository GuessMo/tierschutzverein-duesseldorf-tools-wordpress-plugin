const RATIO_FORMAT = new Intl.NumberFormat("de-DE", {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
});

export function contrastLabel(ratio: number | null, minimum: number | null): string {
  if (ratio === null) return "Kontrast nicht messbar";
  const value = `Kontrast ${RATIO_FORMAT.format(ratio)}:1`;
  if (minimum === null) return `${value} · dekorativ`;
  const target = `min. ${RATIO_FORMAT.format(minimum)}:1`;
  return ratio >= minimum ? `${value} · erfüllt (${target})` : `${value} · unter ${target}`;
}
