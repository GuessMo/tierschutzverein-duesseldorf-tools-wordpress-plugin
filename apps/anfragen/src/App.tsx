import { anfragenApi } from "@/api";
import { StartupScreen } from "@/components/workspace";
import { Workspace } from "@/components/workspace";
import { useResource } from "@/hooks";

export function App() {
  const context = useResource(anfragenApi.context);
  if (!context.data) return <StartupScreen error={context.error} onRetry={context.reload} />;
  return <Workspace context={context.data} />;
}
