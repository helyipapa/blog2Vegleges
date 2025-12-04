# Blog2 API dokumentáció

Ez a dokumentáció a projekt `routes/api.php`-ban definiált egyszerű REST API-t írja le (Users, Posts, Comments). A példák feltételezik, hogy az alkalmazás a Laravel projekt gyökérkönyvtárában fut, a végpontok alapja:

- Alap URL (lokál): http://localhost (ha az app a webroot alatt van, akkor például `http://localhost/blog` — ekkor az API: `http://localhost/blog/api/...`)
- Laravel API prefix: /api

Nincs beépített autentikáció a demo végpontokra (minden GET/POST művelet elérhető). Éles alkalmazásban használj autentikációt (Sanctum/JWT) és jogosultságkezelést.

## Modellek és mezők

- User
  - id (int)
  - name (string)
  - email (string)
  - role (string) — pl. admin/author/user
  - created_at, updated_at (timestamps)

- Post
  - id (int)
  - user_id (int) — szerző, idegen kulcs a users táblára
  - title (string)
  - content (text)
  - published_at (datetime, nullable)
  - created_at, updated_at

- Comment
  - id (int)
  - user_id (int) — hozzászóló, idegen kulcs a users táblára
  - post_id (int) — a poszthoz tartozik
  - content (text)
  - created_at, updated_at

## Általános szabályok

- Minden POST kérés Content-Type: application/json legyen.
- Hibák JSON-ben térnek vissza: { "message": "..." } vagy Laravel validáció esetén a megszokott 422 válasz szerkezet.
- Sikeres létrehozásnál 201 Created választ adunk vissza és a létrejött erőforrás azonosítóját (vagy a teljes erőforrást) küldjük.

## Végpontok

1) Users

- GET /api/users
  - Leírás: Felhasználók listája (alapvető mezőkkel).
  - Válasz (200):

```json
[ { "id": 1, "name": "Kovács Ádám", "email": "adam.kovacs@example.hu", "role": "admin", "created_at": "2025-12-04T10:00:00Z", "updated_at": "2025-12-04T10:00:00Z" } ]
```

- GET /api/users/{id}
  - Leírás: Egy felhasználó részletei.
  - Válasz (200):

```json
{ "id": 2, "name": "Nagy Eszter", "email": "eszter.nagy@example.hu", "role": "author", "created_at": "2025-12-04T10:00:00Z", "updated_at": "2025-12-04T10:00:00Z" }
```

- POST /api/users
  - Leírás: Új felhasználó létrehozása.
  - Kötelező mezők: name (string), email (string, egyedi), password (string, min:6)
  - Példa kérés (JSON):

```json
{
  "name": "Kovács János",
  "email": "janos.kovacs@example.hu",
  "password": "password",
  "role": "user"
}
```

  - Sikeres válasz (201):

```json
{ "id": 5 }
```

  - Hibák: 422 validation (pl. email már létezik vagy hiányzó mezők)

2) Posts

- GET /api/posts
  - Leírás: Posztok listája (szerző nevét is tartalmazza).
  - Válasz (200):

```json
[ { "id":1, "title":"A magyar gasztronómia titkai", "published_at":"2025-12-04T10:10:00Z", "created_at":"2025-12-04T10:10:00Z", "author": { "id":2, "name":"Nagy Eszter" } } ]
```

- GET /api/posts/{id}
  - Leírás: Egy poszt teljes részlete (szerző és hozzászólások betöltve).
  - Válasz (200):

```json
{
  "id": 1,
  "user_id": 2,
  "title": "A magyar gasztronómia titkai",
  "content": "Magyar ételek...",
  "published_at": "2025-12-04T10:10:00Z",
  "created_at": "2025-12-04T10:10:00Z",
  "updated_at": "2025-12-04T10:10:00Z",
  "user": { "id": 2, "name": "Nagy Eszter", "email": "eszter.nagy@example.hu" },
  "comments": [ { "id": 1, "post_id": 1, "content": "Szuper cikk...", "user": { "id":3, "name":"Tóth Péter" } } ]
}
```

- POST /api/posts
  - Leírás: Új poszt létrehozása.
  - Kötelező mezők: user_id (létező user), title, content
  - Példa JSON:

```json
{
  "user_id": 2,
  "title": "Új poszt",
  "content": "Ez egy teszt poszt.",
  "published_at": "2025-12-05 12:00:00"
}
```

  - Sikeres válasz (201):

```json
{ "id": 10 }
```

3) Comments

- GET /api/comments
  - Leírás: Hozzászólások listája (szerző neve és post_id szerepel).

- GET /api/comments/{id}
  - Leírás: Egy hozzászólás részlete (szerző és poszt szerepel).

- POST /api/comments
  - Leírás: Új hozzászólás létrehozása.
  - Kötelező mezők: user_id (létező), post_id (létező), content
  - Példa kérés:

```json
{
  "user_id": 3,
  "post_id": 1,
  "content": "Jó bejegyzés!"
}
```

  - Sikeres válasz (201):

```json
{ "id": 12 }
```

## Hibaválaszok és validáció

- Hiányzó mezők vagy hibás formátum: HTTP 422 (Laravel validation response), formátuma:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

- Nem található erőforrás: HTTP 404 { "message": "No query results for model" } vagy testreszabott hiba.

## Példa curl parancsok

- List users:

```powershell
curl -s http://localhost/api/users | jq
```

- Create user:

```powershell
curl -X POST http://localhost/api/users -H "Content-Type: application/json" -d '{"name":"Teszt","email":"teszt@example.hu","password":"password"}'
```

## Postman

- A `postman_collection.json` importálásával gyorsan kipróbálhatod a fenti végpontokat. (Ha nincs, egyszerűen hozz létre a fenti URL-ekkel kézi kéréseket.)

## Lokális indítás / tennivalók

1. Győződj meg róla, hogy a `.env` fájlban a DB beállítások helyesek (DB_DATABASE=blog2, DB_USERNAME=root, DB_PASSWORD=). A projekt gyökere: `c:/xampp1/htdocs/blog2/blog`.
2. Hozd létre a DB-t (phpMyAdmin vagy CLI):

```powershell
mysql -u root -e "CREATE DATABASE IF NOT EXISTS `blog2` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

3. Fuss a migrációk és a seeder:

```powershell
cd 'c:\xampp1\htdocs\blog2\blog'
php artisan migrate --seed --force
```

4. Indítsd a beépített szervert (fejlesztéshez):

```powershell
php artisan serve --port=8000
# majd a fenti végpontok elérhetők: http://127.0.0.1:8000/api/posts
```

Vagy használd az XAMPP + Apache beállítást és helyezd a Laravel `public` mappáját a webroot alatt megfelelő virtual host-tal.

## Kapcsolatok (relations) röviden

- User hasMany Posts
- User hasMany Comments
- Post belongsTo User
- Post hasMany Comments
- Comment belongsTo User
- Comment belongsTo Post

---

Ha szeretnéd, hogy hozzáadjak részletes OpenAPI (Swagger) specifikációt vagy generáljak egy Postman-gyűjteményt a projekt aktuális útvonalaival és tesztekkel, megcsinálom — mondj egyet a következő lehetőségek közül:

- OpenAPI (YAML) generálása
- Postman collection + Postman tests (automatikus ellenőrzés)
- Swagger UI integráció (csomag telepítése és egy route hozzáadása)
