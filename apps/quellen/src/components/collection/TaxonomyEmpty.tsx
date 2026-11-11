import { Button } from "@web-app-kit/frontend/ui";

export function TaxonomyEmpty({ onCreate }: { onCreate: () => void }) {
  return (
    <div className="flex flex-col items-start gap-2 p-2 text-sm text-muted-foreground">
      <p>
        Ordne Quellen mit eigenen Taxonomien, zum Beispiel „Thema“ mit Unterbegriffen oder
        „Schlagwörter“.
      </p>
      <Button variant="outline" size="sm" onClick={onCreate}>
        Taxonomie anlegen
      </Button>
    </div>
  );
}
