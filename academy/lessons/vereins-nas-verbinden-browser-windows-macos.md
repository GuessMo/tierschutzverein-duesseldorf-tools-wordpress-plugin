---
title: "Vereins-NAS verbinden (Browser, Windows, macOS)"
topics: [anwender]
audience: all
updated: 2026-11-10
---
Das Vereins-NAS (UGREEN) erreichst Du im Browser oder als Netzlaufwerk auf Windows und macOS. Benutzername, Passwort und die Adresse für das Netzlaufwerk bekommst Du vom Admin.

## Im Browser

1. Öffne [tierheimcloud.eur6.ug.link](https://tierheimcloud.eur6.ug.link/).
2. Melde Dich mit Deinem NAS-Benutzernamen und Passwort an.
3. Du kannst Dateien ansehen, bearbeiten und hochladen.

## Windows (WebDAV)

Die Netzlaufwerk-Adresse hat die Form `https://name.ddns.net:5006`. Windows braucht sie in einer anderen Schreibweise. Aus `https://name.ddns.net:5006` wird:

```
\\name.ddns.net@SSL@5006\
```

### 1. WebClient-Dienst prüfen

1. Drücke Windows-Taste + R, gib `services.msc` ein und drücke Enter.
2. Suche in der Liste den Dienst **WebClient** und öffne ihn per Doppelklick.
3. Stelle den **Starttyp** auf **Automatisch**.
4. Steht der Status auf „Beendet“, klicke auf **Starten**.
5. Klicke auf **Übernehmen** und **OK**.

### 2. Netzlaufwerk verbinden

1. Öffne den Explorer und klicke links auf **Dieser PC**.
2. Klicke oben auf die drei Punkte und dann auf **Netzlaufwerk verbinden**. Unter Windows 10 ist der Button direkt sichtbar.
3. Wähle einen Laufwerksbuchstaben, zum Beispiel Z:.
4. Trage bei **Ordner** die umgeschriebene Adresse ein.
5. Setze die Haken bei **Verbindung bei Anmeldung wiederherstellen** und **Verbindung mit anderen Anmeldeinformationen herstellen**.
6. Klicke auf **Fertig stellen** und melde Dich mit Deinen NAS-Zugangsdaten an.

Das Laufwerk erscheint danach unter „Dieser PC“.

### Wenn es unter Windows nicht klappt

- Prüfe die Adresse Zeichen für Zeichen, vor allem `@SSL@5006`.
- Prüfe, ob der Dienst WebClient wirklich läuft.
- Verlangt das NAS einen Ordner, hänge ihn an, zum Beispiel `\\name.ddns.net@SSL@5006\Ordnername\`.

## macOS

1. Öffne den Finder und klicke in der Menüleiste auf **Gehe zu → Mit Server verbinden …** (Befehl ⌘ + K).
2. Gib die Adresse ein, zum Beispiel `https://name.ddns.net:5006`, und klicke auf **Verbinden**.
3. Melde Dich mit Deinen NAS-Zugangsdaten an und setze den Haken bei **Passwort im Schlüsselbund sichern**.
4. Das Laufwerk erscheint in der Finder-Seitenleiste.

### Beim Start automatisch verbinden

1. Verbinde das Laufwerk wie oben.
2. Öffne **Systemeinstellungen → Allgemein → Anmeldeobjekte**.
3. Ziehe das Laufwerk aus der Finder-Seitenleiste in die Liste **Beim Anmelden öffnen**.

## Hilfe

Fehlen Dir Zugangsdaten oder die Adresse, wende Dich an den Admin.

## Quellen

- Interne Anleitung „NAS-Zugriff einrichten“
