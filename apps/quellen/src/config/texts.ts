export const TEXTS = {
  collectionsEmpty: [
    "Lege eine Sammlung für ein Projekt an, zum Beispiel für eine Kampagne.",
    "Darin ordnest du Quellen nach eigenen Taxonomien.",
  ].join(" "),
  taxonomyDelete: [
    "Die Taxonomie wird mit allen Begriffen gelöscht.",
    "Die Quellen bleiben erhalten, verlieren aber diese Zuordnungen.",
  ].join(" "),
  termDelete: [
    "Unterbegriffe rücken eine Ebene nach oben.",
    "Quellen verlieren die Zuordnung zu diesem Begriff.",
  ].join(" "),
  collectionDelete: (count: number) => {
    if (count === 0)
      return "Die Sammlung wird mit allen Taxonomien und Begriffen endgültig gelöscht.";
    const sources = count === 1 ? "einer Quelle" : `allen ${count} Quellen`;
    return `Die Sammlung wird mit ${sources}, Taxonomien und Begriffen endgültig gelöscht.`;
  },
};
