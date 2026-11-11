import { IconLayoutOff } from "@tabler/icons-react";
import { Notice } from "./Notice";
import { useSpaceStore } from "@/store/spaceStore";

export function SpaceNotice() {
  const notice = useSpaceStore((state) => state.notice);
  if (!notice) return null;
  return (
    <Notice icon={<IconLayoutOff aria-hidden />} title="Änderung nicht übernommen" role="status">
      {notice}
    </Notice>
  );
}
