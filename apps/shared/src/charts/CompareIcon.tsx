export function CompareIcon({ gap }: { gap: number }) {
  const offset = 10 + Math.min(6, Math.max(0, gap));
  return (
    <svg viewBox="0 0 24 24" aria-hidden className="fill-none stroke-current" strokeWidth={2}>
      <rect x={3} y={6} width={6} height={12} rx={1} />
      <rect x={offset} y={6} width={6} height={12} rx={1} />
    </svg>
  );
}
