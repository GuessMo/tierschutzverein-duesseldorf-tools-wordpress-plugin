const STEPS = [1, 2, 2.5, 5, 10];

export type TickScale = (max: number) => number[];

export function niceTicks(max: number, count = 4): number[] {
  if (max <= 0) return [0, 1];
  const raw = Math.max(1, max / count);
  const magnitude = 10 ** Math.floor(Math.log10(raw));
  const step = (STEPS.find((candidate) => candidate * magnitude >= raw) ?? 10) * magnitude;
  const top = Math.ceil(max / step) * step;
  return Array.from({ length: Math.round(top / step) + 1 }, (_, index) => index * step);
}

export function minuteTicks(max: number): number[] {
  return niceTicks(max / 60).map((minutes) => minutes * 60);
}

export function scoreTicks(): number[] {
  return [0, 25, 50, 75, 100];
}
