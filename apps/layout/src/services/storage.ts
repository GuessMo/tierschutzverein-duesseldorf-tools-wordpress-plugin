import type { LayoutDocument } from "@/types/document";

const DATABASE_NAME = "tsvd-layout";
const STORE_NAME = "documents";

export class StorageError extends Error {}

function openDatabase(): Promise<IDBDatabase> {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(DATABASE_NAME, 1);
    request.onupgradeneeded = () => request.result.createObjectStore(STORE_NAME, { keyPath: "id" });
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(new StorageError("Lokaler Speicher ist nicht verfügbar."));
  });
}

async function runRequest<T>(
  mode: IDBTransactionMode,
  action: (store: IDBObjectStore) => IDBRequest<T>,
) {
  const database = await openDatabase();
  return new Promise<T>((resolve, reject) => {
    const request = action(database.transaction(STORE_NAME, mode).objectStore(STORE_NAME));
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(new StorageError("Speichern im Browser ist fehlgeschlagen."));
  });
}

export function loadDocuments(): Promise<LayoutDocument[]> {
  return runRequest<LayoutDocument[]>("readonly", (store) => store.getAll());
}

export function saveDocument(document: LayoutDocument): Promise<IDBValidKey> {
  return runRequest("readwrite", (store) => store.put(document));
}

export function deleteDocument(documentId: string): Promise<undefined> {
  return runRequest("readwrite", (store) => store.delete(documentId));
}
