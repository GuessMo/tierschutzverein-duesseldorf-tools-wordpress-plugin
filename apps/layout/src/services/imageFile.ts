const MAX_EDGE_PX = 2400;
const JPEG_QUALITY = 0.88;

export class ImageReadError extends Error {}

function loadImage(file: File): Promise<HTMLImageElement> {
  return new Promise((resolve, reject) => {
    const url = URL.createObjectURL(file);
    const image = new Image();
    image.onload = () => resolve(image);
    image.onerror = () => reject(new ImageReadError("Das Bild konnte nicht gelesen werden."));
    image.src = url;
  });
}

export async function readImageFile(file: File): Promise<string> {
  if (!file.type.startsWith("image/")) throw new ImageReadError("Bitte eine Bilddatei wählen.");
  const image = await loadImage(file);
  const scale = Math.min(1, MAX_EDGE_PX / Math.max(image.naturalWidth, image.naturalHeight));
  const canvas = document.createElement("canvas");
  canvas.width = Math.round(image.naturalWidth * scale);
  canvas.height = Math.round(image.naturalHeight * scale);
  canvas.getContext("2d")?.drawImage(image, 0, 0, canvas.width, canvas.height);
  URL.revokeObjectURL(image.src);
  const keepsTransparency = file.type === "image/png" || file.type === "image/svg+xml";
  return keepsTransparency
    ? canvas.toDataURL("image/png")
    : canvas.toDataURL("image/jpeg", JPEG_QUALITY);
}
