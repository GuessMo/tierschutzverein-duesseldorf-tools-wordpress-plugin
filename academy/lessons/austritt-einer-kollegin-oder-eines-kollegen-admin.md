---
title: "Austritt einer Kollegin oder eines Kollegen (Admin)"
topics: [admin, google-workspace, mails]
audience: admin
updated: 2026-11-10
---
Diese Checkliste beschreibt, was beim Austritt einer Kollegin oder eines Kollegen zu tun ist: Mails umleiten, Zugänge sperren, Daten übergeben und später löschen.

## Warum die Mail-Weiterleitung in All-Inkl entsteht

Die MX-Einträge von tierschutzverein-duesseldorf.de und tierheim-duesseldorf.de zeigen auf All-Inkl (w01046a2.kasserver.com). Externe Mails kommen also zuerst bei All-Inkl an, Google bekommt nur, was All-Inkl weiterreicht. Eine Weiterleitung oder ein Alias nur in Google greift deshalb nicht für Mails von außen. Die Weiterleitung einer ausscheidenden Person gehört in den KAS.

## Rollen und Fristen

- Dateien in geteilte Ablagen verschieben: austretende Person, vor dem letzten Arbeitstag.
- Mail-Weiterleitung in All-Inkl (KAS): Admin, letzter Arbeitstag.
- Zugriff sperren (Passwort, Abmeldung erzwingen): Admin, letzter Arbeitstag.
- WordPress-Nutzer herabstufen: Admin, letzter Arbeitstag.
- Vaultwarden-Zugänge entziehen und Passwörter ändern: Admin, letzter Arbeitstag.
- Personenbezogene Daten aus Systemen löschen: Admin, 30 Tage nach Austritt.
- Google-Workspace-Konto löschen: Admin, 30 bis 90 Tage nach Austritt.
- Aufbewahrungsfristen (Lohn, Vertrag): Personalverantwortliche, nach Austritt klären.

## Phase 1: Vorbereitung (vor dem letzten Arbeitstag)

- Persönliche Dateien aus „Meine Ablage“ in geteilte Ablagen verschieben. Was dort bleibt, wird mit dem Konto gelöscht.
- Ordnerstruktur für die Nachfolge festhalten.
- Wiederkehrende Kalendertermine an Team oder Nachfolge übergeben.
- WordPress: Rollen der Person und ihr zugeordnete Beiträge prüfen.
- Vaultwarden: prüfen, welche Einträge der Organisation gehören und welche im privaten Tresor liegen.

## Phase 2: Letzter Arbeitstag

### Mail-Weiterleitung in All-Inkl (KAS)

1. Im KAS unter **E-Mail** die Adresse der Person suchen.
2. Gibt es sie als Postfach: eine Weiterleitung auf die Zieladresse setzen, zum Beispiel kommunikation@ oder die Nachfolge. Das Postfach danach abschalten, damit dort keine Kopien liegen bleiben. Braucht jemand den alten Bestand, vorher per Datenimport übernehmen, siehe „Google-Workspace-Konto für eine Kollegin oder einen Kollegen anlegen“, Schritt 7.
3. Gibt es sie nicht: als Weiterleitungs-Adresse anlegen und das Ziel eintragen.
4. Nach einiger Zeit testen: von einer privaten Adresse an die alte Adresse schreiben. Die Mail muss am Ziel ankommen. All-Inkl übernimmt Änderungen teils zeitversetzt.

### Zugriff sperren (Google Workspace)

1. Admin Console → **Verzeichnis → Nutzer** → Konto öffnen → **Passwort zurücksetzen**.
2. Im Konto unter **Sicherheit → Anmelde-Cookies** (englisch: Security → Sign-in cookies) auf **Zurücksetzen** (Reset) klicken. Das meldet die Person auf allen Geräten ab.
3. Erlaubt es das Abo: über **Mehr** das Konto sperren. Sonst reichen neues Passwort und Cookie-Reset bis zur Löschung.

Das Postfach selbst zu öffnen, ist für die Weiterleitung nicht nötig. Wenn doch: Die Abfrage „Bestätigen Sie, dass Sie es sind“ lässt sich im Konto unter **Sicherheit → Identitätsbestätigung** (englisch: Security → Login challenge → Turn off for 10 mins) für 10 Minuten ausschalten. Das wirkt erst nach einigen Minuten. Hat die Person eine eigene Bestätigung in zwei Schritten, zusätzlich Backup-Codes erzeugen. Ein App-Passwort hilft nur bei IMAP-Programmen, nicht im Browser.

### WordPress

- Nutzer deaktivieren oder auf die Rolle „Abonnent“ setzen. Nicht sofort löschen, sonst gehen Autoren-Zuordnungen verloren.
- Admin- und Redakteursrechte entfernen.

### Vaultwarden

- Web-Tresor → Organisation → **Mitglieder** → Person entfernen.
- Vereinspasswörter ändern, die die Person kannte. API-Schlüssel, SSH-Schlüssel und Server-Zugänge erneuern.

## Phase 3: Nach dem Austritt (innerhalb von 30 Tagen)

- WordPress: Profilbild, Kontaktdaten und Team-Eintrag entfernen, Beiträge auf andere Autoren übertragen.
- Impressum und Datenschutzerklärung prüfen, falls der Name dort steht.
- Prüfen, ob noch Weiterleitungen oder Abläufe am Konto hängen.
- Aufbewahrungsfristen für Lohn, Vertrag und geschäftliche Mails klären die Personalverantwortlichen.

## Phase 4: Löschung (30 bis 90 Tage nach Austritt)

- Google-Workspace-Konto löschen: **Verzeichnis → Nutzer → Mehr → Nutzer löschen**. Dabei die Datenübertragung nutzen, wenn Inhalte gebraucht werden. Nach dem Löschen ist das Konto nur etwa 20 Tage wiederherstellbar.
- WordPress-Nutzer endgültig löschen, Beiträge vorher übertragen.
- Prüfen, dass die Weiterleitung im KAS weiter greift. Sie hängt nicht am Google-Konto.

## Offene Grundsatzfragen

- Weiterleitungsziel: gemeinsames Postfach wie kommunikation@ oder die konkrete Nachfolge?
- Ein Mailsystem pro Domain festlegen (All-Inkl oder Google). Der Mischbetrieb ist fehleranfällig.
- Social Media: Hatte die Person Zugänge zu Vereinskonten? Dann Passwörter ändern.

## Quellen

- Interne Austritts-Doku (Befund Mail-Routing vom 06.09.2026)
- [Google Workspace-Hilfe: Nutzer aus der Organisation löschen oder entfernen](https://knowledge.workspace.google.com/admin/users/delete-or-remove-a-user-from-your-organization?hl=de)
