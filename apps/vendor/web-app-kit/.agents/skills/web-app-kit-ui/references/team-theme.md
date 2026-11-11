# Theme-Standard dieses Teams

Diese Datei pflegt jedes Team in seinem Kit-Repository selbst. Sie legt fest, welches Theme für
alle Tools des Teams gilt. Agenten lesen sie vor jeder Theme- oder Farbänderung.

## Standard

Hier eintragen: Name und Werte der Primärfarbe (Hell und Dunkel, mit Schriftfarbe), Quelle
(`packages/frontend/src/brand.css`), für welche Tools sie gilt.

Solange hier nichts eingetragen ist, gilt die Primärfarbe aus `brand.css` ohne weitere
Vorgaben.

## Regeln

- Tools übernehmen den Standard unverändert.
- Abweichung eines einzelnen Tools per `applyThemeOverrides` nur mit ausdrücklicher Freigabe des
  Teams, mit Kontrastnachweis in Hell und Dunkel (`references/theme.md`).
- Den Standard ändern: `brand.css` und diese Datei im Kit-Repository anpassen, Kit-Release.
