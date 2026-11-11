const MONTH = new Intl.DateTimeFormat("de-DE", { month: "long", year: "numeric", timeZone: "UTC" });

export function monthLabel(month: string): string {
  const time = Date.parse(`${month}-01T00:00:00Z`);
  return Number.isNaN(time) ? month : MONTH.format(time);
}
