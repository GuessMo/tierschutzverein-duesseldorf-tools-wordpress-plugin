import { IconPhoto } from "@tabler/icons-react";
import sealSvg from "@/assets/tsvd-seal.svg?raw";
import type { ElementKind } from "@/types/document";
import type { ElementRendererProps } from "./artboard.types";
import { textColor } from "./elementStyle";

function Headline({ element }: ElementRendererProps) {
  return (
    <h2 className="el-headline" style={textColor(element)}>
      {element.text}
    </h2>
  );
}

function Paragraph({ element }: ElementRendererProps) {
  return (
    <p className="el-text" style={textColor(element)}>
      {element.text}
    </p>
  );
}

function Contact({ element }: ElementRendererProps) {
  return (
    <address className="el-contact" style={textColor(element)}>
      {element.text}
    </address>
  );
}

function Pill({ element }: ElementRendererProps) {
  return <span className={`el-${element.kind}`}>{element.text}</span>;
}

function Logo({ element }: ElementRendererProps) {
  return (
    <span className="el-logo" style={textColor(element)}>
      <span className="el-logo__seal" dangerouslySetInnerHTML={{ __html: sealSvg }} />
      {element.text && <span className="el-logo__name">{element.text}</span>}
    </span>
  );
}

function Picture({ element }: ElementRendererProps) {
  const shape = element.imageShape ?? "landscape";
  if (!element.imageSrc) {
    return (
      <span className="el-image el-image--empty" data-shape={shape}>
        <IconPhoto aria-hidden="true" />
      </span>
    );
  }
  return <img className="el-image" data-shape={shape} src={element.imageSrc} alt="" />;
}

function Divider() {
  return <hr className="el-divider" />;
}

function Spacer() {
  return <span className="el-spacer" />;
}

export const ELEMENT_RENDERERS: Record<
  ElementKind,
  (props: ElementRendererProps) => React.JSX.Element
> = {
  headline: Headline,
  text: Paragraph,
  contact: Contact,
  button: Pill,
  badge: Pill,
  logo: Logo,
  image: Picture,
  divider: Divider,
  spacer: Spacer,
};
