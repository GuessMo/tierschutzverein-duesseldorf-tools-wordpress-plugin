import type { LinearRgb } from "./contrast.types.js";

const OKLCH_PATTERN = /^oklch\(\s*([\d.]+%?)\s+([\d.]+)\s+([\d.]+)\s*\)$/;
const HEX_PATTERN = /^#([0-9a-f]{6})$/i;

class UnsupportedColorError extends Error {
  constructor(value: string) {
    super(`Unsupported color for contrast check: ${value}`);
    this.name = "UnsupportedColorError";
  }
}

function toLinearChannel(value: number): number {
  return value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4;
}

function clampChannel(value: number): number {
  return Math.min(1, Math.max(0, value));
}

function parseLightness(raw: string): number {
  return raw.endsWith("%") ? Number.parseFloat(raw) / 100 : Number.parseFloat(raw);
}

function oklchToLinearRgb(lightness: number, chroma: number, hueDegrees: number): LinearRgb {
  const hue = (hueDegrees * Math.PI) / 180;
  const a = chroma * Math.cos(hue);
  const b = chroma * Math.sin(hue);
  const l = (lightness + 0.3963377774 * a + 0.2158037573 * b) ** 3;
  const m = (lightness - 0.1055613458 * a - 0.0638541728 * b) ** 3;
  const s = (lightness - 0.0894841775 * a - 1.291485548 * b) ** 3;
  return [
    clampChannel(4.0767416621 * l - 3.3077115913 * m + 0.2309699292 * s),
    clampChannel(-1.2684380046 * l + 2.6097574011 * m - 0.3413193965 * s),
    clampChannel(-0.0041960863 * l - 0.7034186147 * m + 1.707614701 * s),
  ];
}

function hexToLinearRgb(hex: string): LinearRgb {
  const channels = [0, 2, 4].map((index) => Number.parseInt(hex.slice(index, index + 2), 16));
  const [red, green, blue] = channels.map((channel) => toLinearChannel(channel / 255));
  return [red, green, blue];
}

export function toLinearRgb(color: string): LinearRgb {
  const value = color.trim();
  const oklch = OKLCH_PATTERN.exec(value);
  if (oklch) {
    const [, lightness, chroma, hue] = oklch;
    return oklchToLinearRgb(parseLightness(lightness), Number(chroma), Number(hue));
  }
  const hex = HEX_PATTERN.exec(value);
  if (hex) return hexToLinearRgb(hex[1]);
  throw new UnsupportedColorError(value);
}

export function relativeLuminance(color: string): number {
  const [red, green, blue] = toLinearRgb(color);
  return 0.2126 * red + 0.7152 * green + 0.0722 * blue;
}

export function contrastRatio(first: string, second: string): number {
  const [lighter, darker] = [relativeLuminance(first), relativeLuminance(second)].sort(
    (left, right) => right - left,
  );
  return (lighter + 0.05) / (darker + 0.05);
}
