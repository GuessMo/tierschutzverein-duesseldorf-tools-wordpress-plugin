const PARAM = "tier";

export function readAnimalParam(): number {
  const value = Number(new URLSearchParams(window.location.search).get(PARAM));
  return Number.isInteger(value) && value > 0 ? value : 0;
}

export function writeAnimalParam(id: number) {
  const url = new URL(window.location.href);
  if (id > 0) url.searchParams.set(PARAM, String(id));
  else url.searchParams.delete(PARAM);
  window.history.replaceState(null, "", url);
}
