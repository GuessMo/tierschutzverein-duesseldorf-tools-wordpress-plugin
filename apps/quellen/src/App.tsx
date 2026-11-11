import { CollectionScreen } from "@/components/collection";
import { CollectionsScreen } from "@/components/collections";
import { useHashCollection } from "@/hooks";

export function App() {
  const collectionId = useHashCollection();
  if (collectionId === null) return <CollectionsScreen />;
  return <CollectionScreen key={collectionId} collectionId={collectionId} />;
}
