# Blog2 REST API Dokumentáció

**base_url:** `http://127.0.0.1/blog2/public/api` vagy `http://127.0.0.1:8000/api`

Ez a dokumentáció a Blog2 REST API-t írja le, amely Users, Posts és Comments kezelését teszi lehetővé. Az API lehetővé teszi blogbejegyzések létrehozását, szerkesztését, valamint hozzászólások kezelését.

**Funkciók:**
- Felhasználókezelés (CRUD műveletek)
- Blogbejegyzések kezelése (CRUD műveletek)
- Hozzászólások kezelése (CRUD műveletek)
- Kapcsolatok kezelése (felhasználó-bejegyzés-hozzászólás)

Az adatbázis neve: `blog2`

## Végpontok:
A `Content-Type` és az `Accept` headerkulcsok mindig `application/json` formátumúak legyenek.

### Általános hibakezelés:
- 400 Bad Request: Hibás kérés formátum
- 404 Not Found: Az erőforrás nem található
- 422 Unprocessable Entity: Validációs hiba
- 500 Internal Server Error: Szerver hiba

---

## Felhasználókezelés (Users)

### GET `/api/users`

Az összes felhasználó listázása alapvető információkkal.

**Válasz:** `200 OK`
```json
[
  {
    "id": 1,
    "name": "Kovács Ádám",
    "email": "adam.kovacs@example.hu",
    "role": "admin",
    "created_at": "2025-01-15T10:00:00.000000Z",
    "updated_at": "2025-01-15T10:00:00.000000Z"
  },
  {
    "id": 2,
    "name": "Nagy Eszter",
    "email": "eszter.nagy@example.hu",
    "role": "author",
    "created_at": "2025-01-15T10:00:00.000000Z",
    "updated_at": "2025-01-15T10:00:00.000000Z"
  }
]
```

---

### GET `/api/users/{id}`

Egy adott felhasználó részletes adatainak lekérése.

**Paraméterek:**
- `id` (integer, required) - A felhasználó azonosítója

**Válasz:** `200 OK`
```json
{
  "id": 2,
  "name": "Nagy Eszter",
  "email": "eszter.nagy@example.hu",
  "role": "author",
  "created_at": "2025-01-15T10:00:00.000000Z",
  "updated_at": "2025-01-15T10:00:00.000000Z"
}
```

**Hiba válasz:** `404 Not Found`
```json
{
  "message": "User not found"
}
```

---

### POST `/api/users`

Új felhasználó létrehozása.

**Kérés törzse:**
```json
{
  "name": "Kovács János",
  "email": "janos.kovacs@example.hu",
  "password": "password123",
  "role": "user"
}
```

**Kötelező mezők:**
- `name` (string, max:255) - Felhasználó neve
- `email` (string, email, unique) - Email cím
- `password` (string, min:6) - Jelszó

**Opcionális mezők:**
- `role` (string) - Szerepkör (admin/author/user), alapértelmezett: user

**Válasz:** `201 Created`
```json
{
  "id": 5,
  "message": "User created successfully"
}
```

**Hiba válasz:** `422 Unprocessable Entity`
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email has already been taken."
    ],
    "password": [
      "The password must be at least 6 characters."
    ]
  }
}
```

---

## Blogbejegyzés kezelés (Posts)

### GET `/api/posts`

Az összes blogbejegyzés listázása szerző információkkal.

**Válasz:** `200 OK`
```json
[
  {
    "id": 1,
    "title": "A magyar gasztronómia titkai",
    "published_at": "2025-01-15T10:10:00.000000Z",
    "created_at": "2025-01-15T10:10:00.000000Z",
    "author": {
      "id": 2,
      "name": "Nagy Eszter"
    }
  },
  {
    "id": 2,
    "title": "Laravel fejlesztési tippek",
    "published_at": null,
    "created_at": "2025-01-15T11:00:00.000000Z",
    "author": {
      "id": 1,
      "name": "Kovács Ádám"
    }
  }
]
```

---

### GET `/api/posts/{id}`

Egy blogbejegyzés teljes részleteinek lekérése szerzővel és hozzászólásokkal.

**Paraméterek:**
- `id` (integer, required) - A bejegyzés azonosítója

**Válasz:** `200 OK`
```json
{
  "id": 1,
  "user_id": 2,
  "title": "A magyar gasztronómia titkai",
  "content": "A magyar konyha gazdag hagyományokkal rendelkezik...",
  "published_at": "2025-01-15T10:10:00.000000Z",
  "created_at": "2025-01-15T10:10:00.000000Z",
  "updated_at": "2025-01-15T10:10:00.000000Z",
  "user": {
    "id": 2,
    "name": "Nagy Eszter",
    "email": "eszter.nagy@example.hu",
    "role": "author"
  },
  "comments": [
    {
      "id": 1,
      "post_id": 1,
      "content": "Szuper cikk, nagyon hasznos!",
      "created_at": "2025-01-15T12:00:00.000000Z",
      "user": {
        "id": 3,
        "name": "Tóth Péter"
      }
    }
  ]
}
```

**Hiba válasz:** `404 Not Found`
```json
{
  "message": "Post not found"
}
```

---

### POST `/api/posts`

Új blogbejegyzés létrehozása.

**Kérés törzse:**
```json
{
  "user_id": 2,
  "title": "Új blogbejegyzés címe",
  "content": "Ez egy új blogbejegyzés tartalma...",
  "published_at": "2025-01-20 14:00:00"
}
```

**Kötelező mezők:**
- `user_id` (integer, exists:users,id) - Létező felhasználó ID
- `title` (string, max:255) - Bejegyzés címe
- `content` (text) - Bejegyzés tartalma

**Opcionális mezők:**
- `published_at` (datetime, nullable) - Publikálás időpontja (null = draft)

**Válasz:** `201 Created`
```json
{
  "id": 10,
  "message": "Post created successfully"
}
```

**Hiba válasz:** `422 Unprocessable Entity`
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "user_id": [
      "The selected user id is invalid."
    ],
    "title": [
      "The title field is required."
    ]
  }
}
```

---

## Hozzászólás kezelés (Comments)

### GET `/api/comments`

Az összes hozzászólás listázása szerző és bejegyzés információkkal.

**Válasz:** `200 OK`
```json
[
  {
    "id": 1,
    "post_id": 1,
    "content": "Nagyon érdekes cikk!",
    "created_at": "2025-01-15T12:00:00.000000Z",
    "user": {
      "id": 3,
      "name": "Tóth Péter"
    },
    "post": {
      "id": 1,
      "title": "A magyar gasztronómia titkai"
    }
  }
]
```

---

### GET `/api/comments/{id}`

Egy hozzászólás részletes adatainak lekérése.

**Paraméterek:**
- `id` (integer, required) - A hozzászólás azonosítója

**Válasz:** `200 OK`
```json
{
  "id": 1,
  "user_id": 3,
  "post_id": 1,
  "content": "Nagyon érdekes cikk!",
  "created_at": "2025-01-15T12:00:00.000000Z",
  "updated_at": "2025-01-15T12:00:00.000000Z",
  "user": {
    "id": 3,
    "name": "Tóth Péter",
    "email": "peter.toth@example.hu"
  },
  "post": {
    "id": 1,
    "title": "A magyar gasztronómia titkai"
  }
}
```

**Hiba válasz:** `404 Not Found`
```json
{
  "message": "Comment not found"
}
```

---

### POST `/api/comments`

Új hozzászólás létrehozása egy bejegyzéshez.

**Kérés törzse:**
```json
{
  "user_id": 3,
  "post_id": 1,
  "content": "Jó bejegyzés, köszönöm!"
}
```

**Kötelező mezők:**
- `user_id` (integer, exists:users,id) - Létező felhasználó ID
- `post_id` (integer, exists:posts,id) - Létező bejegyzés ID
- `content` (text) - Hozzászólás tartalma

**Válasz:** `201 Created`
```json
{
  "id": 12,
  "message": "Comment created successfully"
}
```

**Hiba válasz:** `422 Unprocessable Entity`
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "post_id": [
      "The selected post id is invalid."
    ],
    "content": [
      "The content field is required."
    ]
  }
}
```

---

## Végpontok összefoglalása

| HTTP metódus | Útvonal           | Státuszkódok                    | Rövid leírás                          |
|--------------|-------------------|---------------------------------|---------------------------------------|
| GET          | /api/users        | 200 OK                          | Felhasználók listázása                |
| GET          | /api/users/{id}   | 200 OK, 404 Not Found           | Egy felhasználó lekérése              |
| POST         | /api/users        | 201 Created, 422 Unprocessable  | Új felhasználó létrehozása            |
| GET          | /api/posts        | 200 OK                          | Bejegyzések listázása                 |
| GET          | /api/posts/{id}   | 200 OK, 404 Not Found           | Egy bejegyzés lekérése                |
| POST         | /api/posts        | 201 Created, 422 Unprocessable  | Új bejegyzés létrehozása              |
| GET          | /api/comments     | 200 OK                          | Hozzászólások listázása               |
| GET          | /api/comments/{id}| 200 OK, 404 Not Found           | Egy hozzászólás lekérése              |
| POST         | /api/comments     | 201 Created, 422 Unprocessable  | Új hozzászólás létrehozása            |

---

## Adatbázis séma

```
+------------------+       +------------------+       +------------------+
|      users       |       |      posts       |       |    comments      |
+------------------+       +------------------+       +------------------+
| id (PK)          |   _1  | id (PK)          |   _1  | id (PK)          |
| name             |  /    | user_id (FK)     |  /    | user_id (FK)     |
| email (unique)   | /     | title            | /     | post_id (FK)     |
| password         |       | content          |       | content          |
| role             |       | published_at     |       | created_at       |
| created_at       |       | created_at       |       | updated_at       |
| updated_at       |       | updated_at       |       +------------------+
+------------------+       +------------------+

Kapcsolatok:
- User hasMany Posts (1:N)
- User hasMany Comments (1:N)
- Post belongsTo User (N:1)
- Post hasMany Comments (1:N)
- Comment belongsTo User (N:1)
- Comment belongsTo Post (N:1)
```

---

## Példa cURL parancsok

### Felhasználók listázása
```bash
curl -X GET "http://127.0.0.1:8000/api/users" \
  -H "Accept: application/json"
```

### Új felhasználó létrehozása
```bash
curl -X POST "http://127.0.0.1:8000/api/users" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Teszt Elek",
    "email": "teszt@example.hu",
    "password": "password123",
    "role": "user"
  }'
```

### Bejegyzések listázása
```bash
curl -X GET "http://127.0.0.1:8000/api/posts" \
  -H "Accept: application/json"
```

### Új bejegyzés létrehozása
```bash
curl -X POST "http://127.0.0.1:8000/api/posts" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "user_id": 2,
    "title": "Új bejegyzés",
    "content": "Bejegyzés tartalma...",
    "published_at": "2025-01-20 14:00:00"
  }'
```

### Új hozzászólás létrehozása
```bash
curl -X POST "http://127.0.0.1:8000/api/comments" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "user_id": 3,
    "post_id": 1,
    "content": "Érdekes cikk!"
  }'
```

---

## Lokális telepítés és indítás

### 1. Környezet beállítása

**.env fájl konfiguráció:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog2
DB_USERNAME=root
DB_PASSWORD=
```

**config/app.php módosítása:**
```php
'timezone' => 'Europe/Budapest',
```

### 2. Adatbázis létrehozása

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS blog2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3. Migráció és seeding

```bash
cd c:\blog2
php artisan migrate --seed
```

### 4. Szerver indítása

**Beépített Laravel szerver:**
```bash
php artisan serve --port=8000
```

Ekkor az API elérhető: `http://127.0.0.1:8000/api`

**XAMPP/Apache használata:**
- Helyezd a projektet a `c:\xampp\htdocs\blog2` mappába
- Konfiguráld a virtual host-ot vagy használd közvetlenül
- API elérhető: `http://127.0.0.1/blog2/public/api`

---

## Postman Collection

Importálható Postman gyűjtemény az összes végponttal és példa kérésekkel elérhető a projekt gyökérkönyvtárában: `postman_collection.json`

**Importálás lépései:**
1. Nyisd meg a Postmant
2. Kattints az Import gombra
3. Válaszd ki a `postman_collection.json` fájlt
4. Az összes végpont készen áll a tesztelésre

---

## Fejlesztői jegyzet

Ez egy egyszerű demo API autentikáció és jogosultságkezelés nélkül. Éles környezetben mindenképpen implementáld:
- Laravel Sanctum vagy JWT autentikációt
- Role-based jogosultságkezelést (pl. Policy-k használatával)
- Rate limiting-et
- Input sanitization-t
- Részletesebb hibakezelést és logging-ot

További információkért lásd a `STEP_BY_STEP.md` fájlt.

---

**Verzió:** 1.0.0  
**Laravel verzió:** 11.x  
**Utolsó frissítés:** 2025-01-15  
**Szerző:** Blog2 Development Team
