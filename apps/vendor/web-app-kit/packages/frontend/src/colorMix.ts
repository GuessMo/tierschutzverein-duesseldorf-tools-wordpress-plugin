const OKLCH = String.raw`oklch\(\s*([\d.]+)\s+([\d.]+)\s+([\d.]+)\s*\)`;
const COLOR_MIX_PATTERN = new RegExp(
  String.raw`^color-mix\(\s*in oklch\s*,\s*${OKLCH}\s*,\s*${OKLCH}\s+([\d.]+)%\s*\)$`,
);

function interpolateHue(from: number, to: number, weight: number): number {
  const delta = ((to - from + 540) % 360) - 180;
  return (from + delta * weight + 360) % 360;
}

function round(value: number): number {
  return Number(value.toFixed(6));
}

export function resolveColorMix(value: string): string {
  const match = COLOR_MIX_PATTERN.exec(value.trim());
  if (!match) return value;
  const [fromL, fromC, fromH, toL, toC, toH, percent] = match.slice(1).map(Number);
  const weight = percent / 100;
  const lightness = fromL + (toL - fromL) * weight;
  const chroma = fromC + (toC - fromC) * weight;
  const hue = interpolateHue(fromH, toH, weight);
  return `oklch(${round(lightness)} ${round(chroma)} ${round(hue)})`;
}
