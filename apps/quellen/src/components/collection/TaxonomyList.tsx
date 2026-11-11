import { ErrorMessage, LoadingRows } from "@/components/common";
import { termsOf } from "@/lib/terms";
import type { TaxonomyListProps } from "./collection.types";
import { TaxonomyEmpty } from "./TaxonomyEmpty";
import { TaxonomySection } from "./TaxonomySection";

export function TaxonomyList({ workspace, onOpen, onCreate }: TaxonomyListProps) {
  const { taxonomies, terms } = workspace;
  const error = taxonomies.error ?? terms.error;
  const retry = () => void Promise.all([taxonomies.reload(), terms.reload()]);

  if (error) return <ErrorMessage message={error} onRetry={retry} />;
  if (!taxonomies.data) return <LoadingRows count={3} label="Taxonomien werden geladen" />;
  if (taxonomies.data.length === 0) return <TaxonomyEmpty onCreate={onCreate} />;
  return (
    <div className="flex flex-col divide-y divide-sidebar-border">
      {taxonomies.data.map((taxonomy) => (
        <TaxonomySection
          key={taxonomy.id}
          taxonomy={taxonomy}
          terms={termsOf(terms.data ?? [], taxonomy.id)}
          onOpen={onOpen}
        />
      ))}
    </div>
  );
}
