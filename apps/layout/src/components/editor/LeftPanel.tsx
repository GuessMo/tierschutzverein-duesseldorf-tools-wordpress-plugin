import { SidePanel } from "@web-app-kit/frontend/ui";
import { AddSectionPanel } from "./AddSectionPanel";
import { ElementPalette } from "./ElementPalette";
import { PageList } from "./PageList";
import { SectionList } from "./SectionList";

const FLUSH_BODY = "[&>[data-slot=side-panel-body]]:p-0";

export function LeftPanel() {
  return (
    <SidePanel side="left" width="md" className={FLUSH_BODY}>
      <PageList />
      <SectionList />
      <ElementPalette />
      <AddSectionPanel />
    </SidePanel>
  );
}
