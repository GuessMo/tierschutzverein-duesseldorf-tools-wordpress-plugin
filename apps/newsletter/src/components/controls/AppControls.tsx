import { HeaderControls } from "@tsvd/tool-controls";
import { readBootstrap } from "@/config/bootstrap";

export function AppControls() {
  return <HeaderControls {...readBootstrap()} />;
}
