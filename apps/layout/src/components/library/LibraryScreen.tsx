import { AppShell, PageContainer } from "@web-app-kit/frontend/ui";
import { AppControls } from "@/components/controls";
import { useLibraryStore } from "@/store/libraryStore";
import { DocumentList } from "./DocumentList";
import { TemplateGallery } from "./TemplateGallery";

export function LibraryScreen() {
  const errorMessage = useLibraryStore((state) => state.errorMessage);

  return (
    <AppShell brand="Layout" skipLinkLabel="Zum Inhalt springen" controls={<AppControls />}>
      <PageContainer className="flex flex-col gap-10">
        {errorMessage && (
          <p className="text-sm text-destructive" role="alert">
            {errorMessage}
          </p>
        )}
        <DocumentList />
        <TemplateGallery />
      </PageContainer>
    </AppShell>
  );
}
