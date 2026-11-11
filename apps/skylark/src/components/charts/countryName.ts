const NAMES = new Intl.DisplayNames(["de"], { type: "region" });

export function countryName(code: string): string {
  try {
    return NAMES.of(code.toUpperCase()) ?? code;
  } catch {
    return code;
  }
}
