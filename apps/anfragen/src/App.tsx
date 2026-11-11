import { anfragenApi } from "@/api";
import { AreaNav, RelayedShell, StartupScreen, Workspace } from "@/components/workspace";
import { useArea, useResource } from "@/hooks";

export function App() {
  const context = useResource(anfragenApi.context);
  const [area, setArea] = useArea();
  if (!context.data) return <StartupScreen error={context.error} onRetry={context.reload} />;
  const nav = <AreaNav area={area} onChange={setArea} />;
  if (area === "relayed") return <RelayedShell nav={nav} />;
  return <Workspace context={context.data} nav={nav} />;
}
