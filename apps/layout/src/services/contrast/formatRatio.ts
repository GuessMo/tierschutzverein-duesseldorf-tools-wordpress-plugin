const RATIO_FORMAT = new Intl.NumberFormat("de-DE", {
  maximumFractionDigits: 1,
  minimumFractionDigits: 1,
});

export function formatRatio(ratio: number): string {
  return `${RATIO_FORMAT.format(Math.floor(ratio * 10) / 10)}:1`;
}
