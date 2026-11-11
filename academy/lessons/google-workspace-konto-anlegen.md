---
title: "Google-Workspace-Konto für eine Kollegin oder einen Kollegen anlegen"
topics: [admin, google-workspace, mails]
audience: admin
updated: 2026-11-10
---
Diese Anleitung beschreibt, wie Du als Admin ein Google-Workspace-Konto für eine Kollegin oder einen Kollegen anlegst. Am Ende hat die Person eine eigene Vereinsadresse, ein Startpasswort und weiß, wie sie ihre Mails zusätzlich in Gmail bekommt.

Die Einrichtung besteht aus zwei Teilen: Du legst das Konto an und übergibst die Zugangsdaten. Die Weiterleitung von All-Inkl zu Google trägt die Person selbst ein, dafür gibt es die separate Anleitung *Weiterleitung von All-Inkl zu Deinem Google-Konto einrichten*.

## Voraussetzungen

- Super-Admin-Zugang zur Google Admin Console unter `admin.google.com`.
- Die Rolle **Nutzerverwaltung**. Fehlt sie, sind Teile des Menüs ausgeblendet.
- Eine freie Google-Workspace-Lizenz. Ohne Lizenz bricht das Anlegen mit einem Lizenzhinweis ab.
- Die gewünschte Adresse, zum Beispiel **vorname.nachname@tierschutzverein-duesseldorf.de**.

## 1. Konto in der Admin Console anlegen

1. Öffne die Admin Console und melde Dich als Super-Admin an.
2. Öffne im Menü **Verzeichnis → Nutzer**. Direktlink: `admin.google.com/ac/users`.
3. Klicke oben in der Nutzerliste auf **Neuen Nutzer hinzufügen**.
4. Trage **Vorname** und **Nachname** ein.
5. Trage die **primäre E-Mail-Adresse** ein. Hat das Konto mehrere Domains, wähle rechts vom @-Zeichen die Domain aus.
6. Trage eine **sekundäre E-Mail-Adresse** ein. An sie gehen die Kontodetails, falls die Vereinsadresse noch nicht erreichbar ist.
7. Klicke auf **Passwort, Organisationseinheit und Profilbild des Nutzers verwalten** und setze dort ein Startpasswort.
8. Klicke auf **Neuen Nutzer hinzufügen**.

**Wichtig:** Es kann bis zu 24 Stunden dauern, bis alle Google-Dienste für das neue Konto bereitstehen. In dieser Zeit kann die Person bereits angelegt sein und trotzdem noch keinen Zugriff auf Gmail haben.

## 2. Passwort setzen und übergeben

Nach dem Anlegen zeigt die Console zwei Wege für das Passwort an:

- **Passwort kopieren** und der Person persönlich übergeben. Das ist der sichere Weg im Verein.
- **Vorschau anzeigen und senden**, um die Kontodetails per Mail zu schicken.

Die Willkommensmail enthält einen Link zum Zurücksetzen des Passworts. Dieser Link gilt 48 Stunden.

Bitte die Person, das Startpasswort bei der ersten Anmeldung zu ändern und die Zwei-Faktor-Anmeldung einzurichten. Passwörter gehören nie in eine Rundmail oder in einen Chat mit mehreren Personen.

## 3. Alias prüfen, wenn die Person eine zweite Adresse nutzt

Manche Konten laufen über die Adresse **@tierheim-duesseldorf.de** statt über die primäre Vereinsadresse. Für diese Konten muss die primäre Domain als Alias hinterlegt sein, sonst kommt die Weiterleitung aus All-Inkl nicht an.

1. Öffne in der Admin Console **Verzeichnis → Nutzer** und klicke die Person an.
2. Öffne den Bereich für **alternative E-Mail-Adressen** und trage die primäre Adresse als Alias ein.
3. Ein Alias empfängt nur. Antworten über den Alias richtet die Person selbst in Gmail unter **Konten → Andere E-Mail-Adresse hinzufügen** ein. In der Admin Console ist das nicht möglich.

## 4. E-Mail-Delegierung freischalten (einmalig)

Die Delegierung erlaubt es, ein Postfach im eigenen Gmail zu öffnen, ohne das Passwort zu teilen. Das brauchst Du zum Beispiel für gemeinsame Postfächer wie **kontakt@**.

1. Öffne in der Admin Console **Apps → Google Workspace → Gmail → Nutzereinstellungen**.
2. Öffne **E-Mail-Delegierung**.
3. Aktiviere die Option, mit der Nutzer anderen Nutzern der Domain Zugriff auf ihr Postfach geben dürfen.
4. Klicke auf **Speichern**.

**Wichtig:** Delegierte lassen sich nicht in der Admin Console eintragen. Die Console schaltet die Funktion nur frei. Wer Zugriff bekommt, legt der Kontoinhaber selbst in Gmail fest.

## 5. Weiterleitung an All-Inkl übergeben

Der MX-Eintrag der Domain zeigt auf All-Inkl. Externe Mail an eine Vereinsadresse landet also zuerst bei All-Inkl und nicht bei Google. Damit sie zusätzlich in Gmail ankommt, braucht es im All-Inkl-Webmailer einen **Kopieempfänger**.

Diese Einstellung nimmt die Person selbst vor. Gib ihr dafür zwei Dinge mit:

- ihre vollständige Google-Adresse,
- die Weiterleitungsadresse in der Form `vorname.nachname@tierschutzverein-duesseldorf.de.test-google-a.com`.

Die Endung `test-google-a.com` ist ein von Google bereitgestellter Alias. Sie sorgt dafür, dass die Mail ohne Änderung des MX-Eintrags bei Google ankommt. Als Ziel darf nie dieselbe **@tierschutzverein-duesseldorf.de**-Adresse eingetragen werden, sonst läuft die Mail im Kreis.

## 6. Testen

- Die Person meldet sich in Gmail an und sieht ihre Adresse.
- Eine Mail von einer privaten Adresse an die Vereinsadresse kommt im All-Inkl-Postfach und in Gmail an.
- Eine Mail aus Gmail an eine externe Adresse kommt an.

## 7. Alte Mails aus All-Inkl übernehmen (optional)

Der Kopieempfänger bringt nur neue Mails nach Gmail. Den bisherigen Bestand im All-Inkl-Postfach überträgt der Datenimport von Google per IMAP. Das All-Inkl-Postfach bleibt dabei unverändert. Das gilt für persönliche Konten genauso wie für gemeinsame Postfächer wie kommunikation@.

**Voraussetzungen:** Super-Admin-Zugang, das Google-Konto ist angelegt, und Du kennst das Passwort des All-Inkl-Postfachs (Vaultwarden, sonst im KAS zurücksetzen).

1. Halte die Vereinsadresse und das All-Inkl-Webmail-Passwort des Postfachs bereit. Öffne den IMAP-Import direkt: `admin.google.com/ac/migrate/imap?journey=218`. Der Menüweg ist **Daten → Datenimport und -export → Datenimport** (englisch: Data → Data import & export → Data import).
2. Kommst Du über das Menü, klicke bei **IMAP** auf **Importieren**.
3. Trage als IMAP-Server `w01046a2.kasserver.com` ein. All-Inkl steht nicht in Googles Anbieterliste, die Adresse also von Hand eintragen. Klicke auf **Verbindung testen**.
4. Trage die Nutzer ein. Für einzelne Postfächer direkt: IMAP-E-Mail-Adresse, IMAP-Passwort und Workspace-Adresse, dann **Nutzer hinzufügen** und **Änderungen speichern**. Für mehrere: unter „Schritt 2: Nutzerinformationen hinzufügen“ die Muster-CSV herunterladen, je Zeile Source ImapUser, Source ImapPassword und TargetGUser ausfüllen und hochladen. Die CSV enthält Passwörter, lösche sie nach dem Upload.
5. Optional: Startdatum festlegen. Gelöschte Mails und Spam nur importieren, wenn sie gebraucht werden.
6. Klicke auf **Import starten**. Die Seite zeigt den Fortschritt. Am Ende kommt eine Mail mit der Statistik.
7. Kommen danach noch Mails ins All-Inkl-Postfach, die nicht in Gmail sind, holt ein **Delta-Import** sie nach.

**Wichtig:** Beim Start erteilt Google dem Datenimport eine domänenweite Delegierung. Das Postfach-Passwort gehört nicht in Mails oder Chats. Für Gruppen-Archive gibt es diesen Weg nicht, sie bleiben in der Gruppe.

## Stolpersteine

- **Menüpunkt „Nutzer“ fehlt:** Die Rolle Nutzerverwaltung ist nicht gesetzt.
- **Anlegen bricht mit Lizenzhinweis ab:** Es ist keine Lizenz frei. Prüfe unter Abrechnung → Abos.
- **Konto existiert, Gmail meldet aber fehlenden Zugriff:** Bis zu 24 Stunden warten.
- **Mail kommt bei All-Inkl an, aber nicht in Gmail:** Der Kopieempfänger fehlt oder ist noch nicht aktiv. All-Inkl greift teils zeitversetzt, deshalb nach 15 bis 30 Minuten erneut testen.
- **Weiterleitung kommt nicht an, obwohl sie stimmt:** Prüfe, ob bei einem Konto mit @tierheim-duesseldorf.de der Alias auf die primäre Adresse gesetzt ist.
- **Google meldet „550 5.1.1 … does not exist“:** Der Name vor dem @-Zeichen in der Weiterleitung passt nicht zum Alias. Beispiel: Der Alias lautet `v.nachname@tierschutzverein-duesseldorf.de`, die Weiterleitung geht aber an `vorname.nachname@tierschutzverein-duesseldorf.de.test-google-a.com`. Der Teil vor dem @-Zeichen muss in beiden genau gleich sein. Passe die Weiterleitung an oder lege einen passenden Alias an. Ein neuer Alias braucht laut Google bis zu 24 Stunden.
- **Verbindungstest schlägt fehl:** Prüfe die Serveradresse `w01046a2.kasserver.com`. Der Domainname funktioniert dort nicht.
- **Anmeldung beim Import abgelehnt:** Prüfe das Postfach-Passwort im All-Inkl-Webmailer. Nimmt All-Inkl die Adresse nicht als Benutzernamen an, den Postfach-Benutzernamen aus dem KAS eintragen.

## Quellen

- [Google Workspace-Hilfe: Konto für neue Nutzer hinzufügen](https://knowledge.workspace.google.com/admin/users/add-an-account-for-a-new-user?hl=de)
- [Google Workspace-Hilfe: Alternative E-Mail-Adresse hinzufügen oder löschen](https://knowledge.workspace.google.com/admin/users/add-or-delete-an-alternate-email-address-email-alias?hl=de)
- [Google Workspace-Hilfe: Gmail-Delegierung für Nutzer zulassen](https://knowledge.workspace.google.com/admin/gmail/let-users-delegate-access-to-a-gmail-account?hl=de)
- [Google Workspace-Hilfe: E-Mails mit dem Datenimport-Tool importieren](https://knowledge.workspace.google.com/admin/migrate/import-email-with-the-data-import-tool?hl=de)
- [All-Inkl-FAQ: Server und Ports (IMAPS 993)](https://all-inkl.com/wichtig)
