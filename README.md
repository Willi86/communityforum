# CommunityHub

CommunityHub är ett communityforum byggt med PHP och SQL för kursens inlämningsuppgift.

## Mål

Projektet byggs för betyget **G**.

Funktionerna som ska finnas:

- Skapa användarkonto
- Logga in och logga ut
- Skapa grupper
- Se grupper som användaren inte är medlem i
- Ansöka om medlemskap
- Gruppmedlemmar kan godkänna medlemsansökningar
- Gruppmedlemmar kan starta diskussioner
- Gruppmedlemmar kan svara på diskussioner
- Säker åtkomstkontroll till gruppdata

## Teknik

- PHP
- MySQL
- PDO
- HTML
- CSS
- Enkel JavaScript vid behov

Inga JavaScript-ramverk används.

## Lokal installation

1. Starta Apache och MySQL i XAMPP.
2. Skapa databasen genom att köra `database/schema.sql`.
3. Kopiera `config/config.example.php` till `config/config.php`.
4. Fyll i de lokala databasuppgifterna i `config/config.php`.
5. Starta projektet med `public/` som webbrot.

`config/config.php` ligger i `.gitignore` eftersom filen kan innehålla lokala databasuppgifter.

## Databas

Projektet använder följande tabeller:

- `users`
- `forum_groups`
- `group_members`
- `group_join_requests`
- `discussions`
- `posts`

## Säkerhet

Projektet kommer att använda:

- PDO prepared statements
- `password_hash()` och `password_verify()`
- PHP-sessioner
- server-side behörighetskontroll
- CSRF-skydd
- `htmlspecialchars()` vid utskrift av användardata
- validering av användarinput

## Git

Projektet utvecklas stegvis med tydliga commits under hela arbetet.
