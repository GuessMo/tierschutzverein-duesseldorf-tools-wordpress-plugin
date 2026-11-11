import {
  IconAddressBook,
  IconAlignLeft,
  IconHeading,
  IconPaw,
  IconPhoto,
  IconPointer,
  IconRosette,
  IconSeparatorHorizontal,
  IconSpacingVertical,
  type Icon,
} from "@tabler/icons-react";
import type { ElementKind } from "@/types/document";

export const ELEMENT_ICONS: Record<ElementKind, Icon> = {
  headline: IconHeading,
  text: IconAlignLeft,
  image: IconPhoto,
  logo: IconPaw,
  button: IconPointer,
  contact: IconAddressBook,
  badge: IconRosette,
  divider: IconSeparatorHorizontal,
  spacer: IconSpacingVertical,
};
