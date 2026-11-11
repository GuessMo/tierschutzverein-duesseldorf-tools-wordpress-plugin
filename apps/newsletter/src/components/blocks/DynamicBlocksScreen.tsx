import { IconExternalLink } from "@tabler/icons-react";
import {
  Button,
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  PageContainer,
} from "@web-app-kit/frontend/ui";
import { newsletterApi } from "@/api";
import { ErrorMessage, LoadingRows } from "@/components/common";
import { useResource } from "@/hooks";

const INTRO =
  "Diese Blöcke füllen sich beim Versand selbst mit allem, was noch in keinem Newsletter war.";

export function DynamicBlocksScreen() {
  const index = useResource(newsletterApi.index);
  const dynamic = index.data?.blockTypes.filter((type) => type.dynamic) ?? [];
  return (
    <PageContainer size="md" className="flex flex-col gap-6">
      <div className="flex flex-col gap-1">
        <h1 className="text-2xl font-semibold">Dynamische Blöcke</h1>
        <p className="text-sm text-muted-foreground">{INTRO}</p>
      </div>
      {index.error && <ErrorMessage message={index.error} onRetry={() => void index.reload()} />}
      {!index.data && !index.error && <LoadingRows label="Blöcke werden geladen" count={2} />}
      {dynamic.map((block) => (
        <Card key={block.type}>
          <CardHeader>
            <CardTitle asChild>
              <h2>{block.label}</h2>
            </CardTitle>
          </CardHeader>
          <CardContent className="flex flex-col gap-3 text-sm">
            <p>{block.description}</p>
            <p className="text-muted-foreground">{block.status}</p>
            {block.manageUrl && (
              <Button asChild variant="outline" size="sm" className="self-start">
                <a href={block.manageUrl} target="_blank" rel="noopener">
                  <IconExternalLink data-icon="inline-start" />
                  Inhalte verwalten
                </a>
              </Button>
            )}
          </CardContent>
        </Card>
      ))}
    </PageContainer>
  );
}
