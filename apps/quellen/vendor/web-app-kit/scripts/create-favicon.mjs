#!/usr/bin/env node
import { mkdirSync, readFileSync, writeFileSync } from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { parseArgs } from "node:util";

const KIT_ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const TEMPLATE_PATH = path.join(KIT_ROOT, "scripts/templates/favicon.template.svg");
const DEFAULT_BRAND_PATH = path.join(KIT_ROOT, "packages/frontend/src/brand.css");
const DEFAULT_OUT_PATH = "frontend/public/favicon.svg";
const OKLCH_PATTERN = /^oklch\([^()]*\)$/;
const ICON_ATTRIBUTES = ["fill", "stroke", "stroke-width", "stroke-linecap", "stroke-linejoin"];
const TEXT_FONT_SIZES = { 1: 20, 2: 15 };
const XML_ENTITIES = { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&apos;" };
const USAGE =
  "Usage: create-favicon.mjs (--text <1-2 Zeichen> | --icon <tabler.svg>) " +
  "[--out <datei>] [--brand <brand.css>]";

class FaviconError extends Error {
  constructor(message) {
    super(message);
    this.name = "FaviconError";
  }
}

class UsageError extends FaviconError {}

function parseCliArgs(argv) {
  const options = {
    text: { type: "string" },
    icon: { type: "string" },
    out: { type: "string", default: DEFAULT_OUT_PATH },
    brand: { type: "string", default: DEFAULT_BRAND_PATH },
  };
  try {
    const { values } = parseArgs({ args: argv, options, allowPositionals: false });
    if ((values.text === undefined) === (values.icon === undefined)) throw new UsageError(USAGE);
    return values;
  } catch (error) {
    if (error instanceof UsageError) throw error;
    throw new UsageError(`${error.message}\n${USAGE}`);
  }
}

function readBlock(css, selector, cssPath) {
  const escaped = selector.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
  const match = css.match(new RegExp(`(?:^|\\n)\\s*${escaped}\\s*\\{([^}]*)\\}`));
  if (!match) throw new FaviconError(`${cssPath}: Block ${selector} fehlt.`);
  return match[1];
}

function readColor(block, name, location) {
  const match = block.match(new RegExp(`(?:^|[;\\s])${name}\\s*:\\s*([^;]+);`));
  if (!match) throw new FaviconError(`${location}: ${name} fehlt.`);
  const value = match[1].trim();
  if (!OKLCH_PATTERN.test(value)) {
    throw new FaviconError(`${location}: ${name} ist kein oklch(L C H)-Wert: ${value}`);
  }
  return value;
}

function readBrandColors(cssPath) {
  const css = readFileSync(cssPath, "utf8");
  const light = readBlock(css, ":root", cssPath);
  const dark = readBlock(css, ".dark", cssPath);
  return {
    PRIMARY_LIGHT: readColor(light, "--primary", `${cssPath} :root`),
    FOREGROUND_LIGHT: readColor(light, "--primary-foreground", `${cssPath} :root`),
    PRIMARY_DARK: readColor(dark, "--primary", `${cssPath} .dark`),
    FOREGROUND_DARK: readColor(dark, "--primary-foreground", `${cssPath} .dark`),
  };
}

function escapeXml(value) {
  return value.replace(/[&<>"']/g, (character) => XML_ENTITIES[character]);
}

function renderTextGlyph(text) {
  const characters = Array.from(text.trim());
  const fontSize = TEXT_FONT_SIZES[characters.length];
  if (!fontSize) throw new FaviconError(`--text braucht 1 oder 2 Zeichen, erhalten: "${text}"`);
  return (
    `<text x="16" y="16" text-anchor="middle" dominant-baseline="central" ` +
    `font-family="system-ui, -apple-system, 'Segoe UI', sans-serif" font-weight="700" ` +
    `font-size="${fontSize}" fill="currentColor">${escapeXml(characters.join(""))}</text>`
  );
}

function readRootAttribute(rootAttributes, name) {
  const match = rootAttributes.match(new RegExp(`\\s${name}="([^"]*)"`));
  return match ? ` ${name}="${match[1]}"` : "";
}

function renderIconGlyph(iconPath) {
  const source = readFileSync(iconPath, "utf8");
  const match = source.match(/<svg\b([^>]*)>([\s\S]*)<\/svg>/);
  if (!match) throw new FaviconError(`${iconPath}: kein <svg>-Element gefunden.`);
  const [, rootAttributes, content] = match;
  if (!/\sviewBox="0 0 24 24"/.test(rootAttributes)) {
    throw new FaviconError(`${iconPath}: erwartet ein Tabler-Icon mit viewBox="0 0 24 24".`);
  }
  const attributes = ICON_ATTRIBUTES.map((name) => readRootAttribute(rootAttributes, name));
  return `<g transform="translate(4 4)"${attributes.join("")}>${content.trim()}</g>`;
}

function fillTemplate(template, values) {
  return template.replace(/\{\{([A-Z_]+)\}\}/g, (placeholder, key) => {
    if (!(key in values)) throw new FaviconError(`Platzhalter ohne Wert: ${placeholder}`);
    return values[key];
  });
}

function createFavicon(argv) {
  const options = parseCliArgs(argv);
  const glyph = options.text === undefined
    ? renderIconGlyph(options.icon)
    : renderTextGlyph(options.text);
  const values = { ...readBrandColors(options.brand), GLYPH: glyph };
  const svg = fillTemplate(readFileSync(TEMPLATE_PATH, "utf8"), values);
  mkdirSync(path.dirname(path.resolve(options.out)), { recursive: true });
  writeFileSync(options.out, svg);
  return options.out;
}

try {
  console.log(`Favicon geschrieben: ${createFavicon(process.argv.slice(2))}`);
} catch (error) {
  if (!(error instanceof FaviconError) && error.code !== "ENOENT") throw error;
  console.error(`FEHLER: ${error.message}`);
  process.exit(error instanceof UsageError ? 64 : 1);
}
