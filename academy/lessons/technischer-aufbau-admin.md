---
title: "Technischer Aufbau (Admin)"
topics: [admin]
audience: admin
updated: 2026-11-04
---
Alle Zugangsdaten (Server-SSH, Proxy Manager, Arcane, Route301, Vaultwarden-Admin, Hetzner Robot, Cloudflare, All-Inkl KAS, Google Admin, GitHub) liegen im Passwortmanager Vaultwarden. In dieser Doku stehen bewusst keine Passwörter.

Stand: 2026-11-04 06:36 UTC – automatisch erzeugt, nicht von Hand bearbeiten.

## Server

| Punkt | Stand |
| --- | --- |
| Betriebssystem | Debian GNU/Linux 12 (bookworm) |
| Kernel (läuft / installiert) | 6.1.0-54-amd64 / 6.1.0-54-amd64 |
| Neustart nötig | nein |
| Laufzeit seit Neustart | up 22 hours, 34 minutes |
| Docker / Compose | 29.9.2 / 5.7.0 |
| Letztes Paket-Update | – |
| Offene Paket-Updates | 0 (davon Sicherheit: 0) |

## Dienste (Container)

| Dienst | Container | Image | Version | Image gebaut | Status | Ports | Compose |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Arcane (Docker-Verwaltung) | `arcane` | `ghcr.io/getarcaneapp/manager:v2.17.2` | v2.17.2 | 2026-10-31 | running | – | `/opt/containers/portainer` |
| Nginx Proxy Manager (Pförtner) | `npm-app-1` | `jc21/nginx-proxy-manager:2.16.0` | 2.16.0 | 2026-09-24 | running | `0.0.0.0:443->443/tcp`, `0.0.0.0:80->80/tcp`, `127.0.0.1:81->81/tcp`, `:::443->443/tcp`, `:::80->80/tcp` | `/opt/containers/npm` |
| Route301 (Weiterleitungen) | `route301_app` | `route301:local` | 8.4.27 | 2026-11-02 | running | `127.0.0.1:12345->80/tcp` | `/opt/containers/route301` |
| MariaDB von Route301 | `route301_db` | `mariadb:11.8.9` | 11.8.9 | 2026-10-26 | running | – | `/opt/containers/route301` |
| WordPress (PHP-FPM) | `tsvd-wp` | `wordpress:php8.3-fpm` | 8.3.36 | 2026-10-28 | running | – | `/opt/containers/tierschutzverein-duesseldorf-de` |
| Vaultwarden (Passwortmanager) | `vaultwarden` | `vaultwarden/server:1.37.5` | 1.37.5 | 2026-11-01 | running | – | `/opt/containers/vaultwarden` |
| MariaDB der Website | `wp-db-neu` | `mariadb:10.11.19` | 10.11.19 | 2026-10-26 | running | – | `/opt/containers/tierschutzverein-duesseldorf-de` |
| nginx vor WordPress | `wp-nginx` | `nginx:1.30.6-alpine` | 1.30.6 | 2026-10-21 | running | `0.0.0.0:8081->80/tcp`, `:::8081->80/tcp` | `/opt/containers/tierschutzverein-duesseldorf-de` |

## Dienste im Detail

### Arcane (Docker-Verwaltung)

| Punkt | Inhalt |
| --- | --- |
| Zweck | Weboberfläche für Container, Images und Logs; hat Vollzugriff auf Docker. |
| Adresse | https://docker.tierschutzverein-duesseldorf.info |
| Daten | `/opt/containers/portainer/data` (SQLite) |
| Hinweise | Seit 2.x Image `ghcr.io/getarcaneapp/manager`; `APP_URL` und `TRUSTED_PROXIES` müssen gesetzt sein. Nie v1 gegen die migrierte DB starten. |

### Nginx Proxy Manager (Pförtner)

| Punkt | Inhalt |
| --- | --- |
| Zweck | Nimmt alle Anfragen auf 80/443 an, stellt TLS-Zertifikate aus und leitet je Domain an den passenden Container weiter. |
| Adresse | Admin nur per SSH-Tunnel: `ssh -N -L 8181:127.0.0.1:81 skylark-prod`, dann `http://localhost:8181` |
| Daten | `/opt/containers/npm/data`, `/opt/containers/npm/letsencrypt` |
| Hinweise | Version fest gepinnt (seit 03.11.2026 auf 2.16.0). Ziel für tierschutzverein-duesseldorf.de zeigt noch auf `94.130.161.164:8081` (Todo Livegang). |

### Route301 (Weiterleitungen)

| Punkt | Inhalt |
| --- | --- |
| Zweck | Symfony-App für 301-Weiterleitungen von alten Adressen; greift erst nach DNS-Umstellung von tierheim-duesseldorf.de. |
| Adresse | https://route301.tierschutzverein-duesseldorf.info |
| Daten | `/opt/containers/route301` (Git-Checkout, Image `route301:local` per `docker compose build --pull app`) |
| Hinweise | `docker/php.ini` mit `session.upload_progress.enabled = 0` (lokale Änderung im Checkout). |

### MariaDB von Route301

| Punkt | Inhalt |
| --- | --- |
| Zweck | Datenbank von Route301. |
| Adresse | nur intern |
| Daten | Docker-Volume in `/opt/containers/route301` |
| Hinweise | – |

### WordPress (PHP-FPM)

| Punkt | Inhalt |
| --- | --- |
| Zweck | Neue Website tierschutzverein-duesseldorf.de, Dashboard, REST/MCP-Schnittstelle. |
| Adresse | https://tierschutzverein-duesseldorf.de/wp-admin |
| Daten | `/opt/containers/tierschutzverein-duesseldorf-de/wp_data` (WP-Root, mu-plugins mit Secrets: `route301-config.php`, `tsvd-sso-config.php`) |
| Hinweise | Extra-PHP-Einstellungen in `php-security.ini` (`session.upload_progress.enabled = 0`). Theme und Plugins kommen per GitHub-Actions-Deploy, nie von Hand ändern. Anmeldung nur mit Google (TSV Tools, `includes/sso/`): Ist SSO eingerichtet, blendet `assets/sso-login.css` auf `wp-login.php` Benutzername, Passwort, „Angemeldet bleiben“, „Anmelden“ und „Passwort vergessen?“ per `display:none` aus. Die Passwort-Anmeldung bleibt serverseitig möglich; im Notfall die Felder über die Browser-Konsole einblenden, z. B. `document.querySelectorAll('#loginform > *, #nav').forEach(e => e.style.display = 'block'); document.getElementById('user_pass').disabled = false;` (WordPress schaltet ein beim Laden verborgenes Passwortfeld nicht frei). |

### Vaultwarden (Passwortmanager)

| Punkt | Inhalt |
| --- | --- |
| Zweck | Bitwarden-kompatibler Passwortmanager des Vereins. |
| Adresse | https://keys.tierschutzverein-duesseldorf.info |
| Daten | `/opt/containers/vaultwarden/vw-data` (SQLite + `rsa_key`) – vor Updates Container stoppen und sichern |
| Hinweise | Version fest gepinnt, Updates nach GitHub-Releases dani-garcia/vaultwarden. |

### MariaDB der Website

| Punkt | Inhalt |
| --- | --- |
| Zweck | Datenbank der neuen Website. |
| Adresse | nur intern (tsvd-network) |
| Daten | `/opt/containers/tierschutzverein-duesseldorf-de/db_data` |
| Hinweise | Root-Passwort steht in der Compose-Umgebung, nie ausgeben. |

### nginx vor WordPress

| Punkt | Inhalt |
| --- | --- |
| Zweck | Liefert statische Dateien und reicht PHP an tsvd-wp weiter. |
| Adresse | intern `wp-nginx:80` (Netz `proxy-tier`), Port 8081 noch öffentlich |
| Daten | `/opt/containers/tierschutzverein-duesseldorf-de/nginx-wp.conf` |
| Hinweise | – |

## Direkt auf dem Server installiert

| Komponente | Version | Status | lauscht auf |
| --- | --- | --- | --- |
| MySQL (Altlast) | 8.4.13 | active | 127.0.0.1:3306 |
| Apache (Altlast) | – | inactive | – |
| PHP CLI auf dem Server | 8.4.27 | – | – |

## Von außen erreichbare Ports

94.130.161.164, geprüft vom lokalen Rechner: 22, 80, 443, 8081

## Bekannte Stolperfallen

- Dedizierter Hetzner-Server (Robot), Firewall nur über Hetzner Robot (zustandslos).
- Host-MySQL mit Altlasten `wp_tsv_prod`/`wp_tsv_stage`, nur noch auf `127.0.0.1` – Abschaltung offen (Todo Livegang).
- Pull von ghcr.io: die gespeicherte Anmeldung in `/root/.docker/config.json` ist ungültig – bis zur Klärung `DOCKER_CONFIG=/tmp/empty-docker-cfg docker pull …` verwenden.
- Software-RAID nur mit einer von zwei NVMe aktiv (`[2/1] [_U]`, seit wann nicht geprüft) – Laufwerk von Hetzner prüfen/tauschen lassen.
- Netzwerkkarte per MAC fest auf `enp5s0` benannt (`/etc/systemd/network/10-wan.link`), weil sich die PCI-Nummer nach dem Neustart am 03.11.2026 von 05 auf 04 verschoben hatte.
