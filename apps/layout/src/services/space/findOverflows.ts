const TOLERANCE_PERCENT = 0.6;

function exceeds(inner: DOMRect, outer: DOMRect, tolerance: number): boolean {
  return (
    inner.top < outer.top - tolerance ||
    inner.bottom > outer.bottom + tolerance ||
    inner.left < outer.left - tolerance ||
    inner.right > outer.right + tolerance
  );
}

function childrenOutside(parents: HTMLElement[], selector: string, tolerance: number) {
  return parents.filter((parent) => {
    const bounds = parent.getBoundingClientRect();
    return Array.from(parent.querySelectorAll<HTMLElement>(`:scope > ${selector}`)).some((child) =>
      exceeds(child.getBoundingClientRect(), bounds, tolerance),
    );
  });
}

export function findOverflows(artboard: HTMLElement): string[] {
  const tolerance = (artboard.offsetWidth * TOLERANCE_PERCENT) / 100;
  const artboardBounds = artboard.getBoundingClientRect();
  const sections = Array.from(artboard.querySelectorAll<HTMLElement>(".artboard-section"));
  const cells = Array.from(artboard.querySelectorAll<HTMLElement>(".artboard-cell"));
  const crowdedCells = childrenOutside(cells, ".artboard-element", tolerance);
  const crowdedSections = childrenOutside(sections, ".artboard-cell", tolerance);
  const clippedSections = sections.filter((section) =>
    exceeds(section.getBoundingClientRect(), artboardBounds, tolerance),
  );
  const ids = [...crowdedCells, ...crowdedSections, ...clippedSections].map(
    (node) => node.dataset.overflowId ?? "",
  );
  return Array.from(new Set(ids)).filter(Boolean);
}
