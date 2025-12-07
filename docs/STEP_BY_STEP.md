# Blog2 — Lépésről Lépésre Telepítési és Tesztelési Útmutató

Ez a dokumentáció segít gyorsan felállítani a Blog2 REST API projektet helyileg (Windows + XAMPP vagy Laravel beépített szerver), lefuttatni a migrációkat, feltölteni a mintadatokat és kipróbálni az API végpontokat.

---

##  Tartalomjegyzék

1. [Előfeltételek](#1-előfeltételek)
2. [Projekt klónozása](#2-projekt-klónozása)
3. [Környezet beállítása](#3-környezet-beállítása)
4. [Függőségek telepítése](#4-függőségek-telepítése)
5. [Adatbázis létrehozása](#5-adatbázis-létrehozása)
6. [Migrációk és seeding](#6-migrációk-és-seeding)
7. [Frontend assets (opcionális)](#7-frontend-assets-opcionális)
8. [Szerver indítása](#8-szerver-indítása)
9. [API tesztelése](#9-api-tesztelése)
10. [Postman használata](#10-postman-használata)
11. [Gyakori problémák](#11-gyakori-problémák)
12. [Tesztelés checklist](#12-tesztelés-checklist)
13. [Feature tesztek](#13-feature-tesztek)

---

## 1. Előfeltételek

### Szükséges szoftverek:

- **Windows 10/11**
- **PHP >= 8.2** (ellenőrizd: `php -v`)
- **Composer** (ellenőrizd: `composer -V`)
- **MySQL** vagy **MariaDB** (XAMPP-al együtt jön)
- **Node.js >= 18** és **npm** (ellenőrizd: `node -v` és `npm -v`)
- **(Opcionális)** Postman az API teszteléshez
- **(Opcionális)** Git verziókezeléshez

### XAMPP telepítés (ajánlott Windows-on):

1. Töltsd le: https://www.apachefriends.org/
2. Telepítsd az Apache és MySQL modulokat
3. Indítsd el a XAMPP Control Panel-t
4. Indítsd el az Apache és MySQL szolgáltatásokat

---

## 2. Projekt klónozása

Ha Git repository-ból klónozod:

```powershell
cd c:\xampp\htdocs
git clone <repository-url> blog2
cd blog2
```

Ha már letöltötted a projektet:

```powershell
cd c:\xampp\htdocs\blog2
```

**Megjegyzés:** A projekt gyökérkönyvtárában (ahol a `composer.json` van) kell lenned.

---

## 3. Környezet beállítása

### 3.1. `.env` fájl létrehozása

Ha nincs `.env` fájl, készíts egyet a `.env.example` alapján:

```powershell
Copy-Item .env.example .env
```

### 3.2. `.env` szerkesztése

Nyisd meg a `.env` fájlt egy szövegszerkesztőben és állítsd be:

```env
APP_NAME="Blog2 API"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Europe/Budapest
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog2
DB_USERNAME=root
DB_PASSWORD=

# Ha van MySQL jelszó (nem alapértelmezett XAMPP):
# DB_PASSWORD=your_password
```

### 3.3. Application key generálása

```powershell
php artisan key:generate
```

Ez automatikusan beállítja az `APP_KEY` értékét a `.env` fájlban.

### 3.4. Timezone ellenőrzése

Nyisd meg `config/app.php` és ellenőrizd:

```php
'timezone' => env('APP_TIMEZONE', 'Europe/Budapest'),
```

---

## 4. Függőségek telepítése

### 4.1. PHP függőségek (Composer)

```powershell
composer install
```

Ha hibát kapsz, próbáld:

```powershell
composer update
composer dump-autoload
```

### 4.2. Node.js függőségek (npm)

A projekt Vite-ot és Tailwind CSS-t használ a frontend számára:

```powershell
npm install
```

Ha hibát kapsz:

```powershell
npm cache clean --force
npm install
```

---

## 5. Adatbázis létrehozása

### 5.1. Grafikus felület (phpMyAdmin)

1. Nyisd meg: http://localhost/phpmyadmin
2. Kattints az "Új" vagy "New" gombra
3. Adatbázis neve: `blog2`
4. Karakterkódolás: `utf8mb4_unicode_ci`
5. Kattints a "Létrehoz" gombra

### 5.2. Parancssor (ajánlott)

**Windows PowerShell:**

```powershell
# XAMPP MySQL helye (általában):
cd C:\xampp\mysql\bin

# Adatbázis létrehozása (ha nincs jelszó):
.\mysql -u root -e "CREATE DATABASE IF NOT EXISTS blog2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ha van jelszó:
.\mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS blog2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Vagy közvetlenül MySQL-ben:**

```powershell
mysql -u root
```

Majd a MySQL prompt-ban:

```sql
CREATE DATABASE IF NOT EXISTS blog2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SHOW DATABASES;
EXIT;
```

---

## 6. Migrációk és seeding

### 6.1. Migrációk állapotának ellenőrzése

```powershell
php artisan migrate:status
```

### 6.2. Migrációk futtatása

**Első futtatás (vagy friss adatbázis):**

```powershell
php artisan migrate --seed
```

**Ha kérdez megerősítést, használd a `--force` flaget:**

```powershell
php artisan migrate --seed --force
```

**Friss kezdés (törli az összes adatot!):**

```powershell
php artisan migrate:fresh --seed
```

### 6.3. Mit csinál a seeder?

A DatabaseSeeder létrehozza:
- **3 felhasználót** (1 admin, 2 szerző)
- **5 blogbejegyzést** különböző szerzőktől
- **10-15 hozzászólást** a bejegyzésekhez

**Ellenőrzés:**

```powershell
php artisan tinker
```

Majd:

```php
>>> User::count();
=> 3

>>> Post::count();
=> 5

>>> Comment::count();
=> 10

>>> exit
```

---

## 7. Frontend assets (opcionális)

Ha a projektet böngészőben is használni akarod (nem csak API):

### 7.1. Development build

```powershell
npm run dev
```

Ez elindít egy Vite dev szervert, amely figyeli a változásokat.

### 7.2. Production build

```powershell
npm run build
```

Ez optimalizált production build-et készít a `public/build` mappába.

**Megjegyzés:** Az API használatához ez nem szükséges, csak ha van frontend is.

---

## 8. Szerver indítása

### Opció A: Laravel beépített szerver (ajánlott fejlesztéshez)

```powershell
php artisan serve
```

Vagy specifikus port megadásával:

```powershell
php artisan serve --port=8000
```

**API elérhető:** http://127.0.0.1:8000/api

**Előnyök:**
- Gyors indítás
- Nincs szükség Apache konfigurációra
- Egyszerű debugging

### Opció B: XAMPP Apache

**1. VirtualHost beállítása (ajánlott):**

Szerkeszd: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/blog2/public"
    ServerName blog2.local
    <Directory "C:/xampp/htdocs/blog2/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Szerkeszd: `C:\Windows\System32\drivers\etc\hosts`

```plaintext
127.0.0.1 blog2.local
```

Indítsd újra az Apache-t, majd az API elérhető: http://blog2.local/api

**2. Vagy közvetlenül htdocs-ból:**

Helyezd a projektet: `C:\xampp\htdocs\blog2`

`.env` frissítése:

```env
APP_URL=http://localhost/blog2/public
```

**API elérhető:** http://localhost/blog2/public/api

---

## 9. API tesztelése

### 9.1. Ping endpoint (gyors teszt)

```powershell
curl http://127.0.0.1:8000/api/users
```

Vagy böngészőben: http://127.0.0.1:8000/api/users

Várt válasz: JSON lista felhasználókkal (200 OK)

### 9.2. cURL példák (PowerShell)

**Users lista:**

```powershell
curl -X GET "http://127.0.0.1:8000/api/users" `
  -H "Accept: application/json"
```

**Egy user lekérése:**

```powershell
curl -X GET "http://127.0.0.1:8000/api/users/1" `
  -H "Accept: application/json"
```

**Új user létrehozása:**

```powershell
curl -X POST "http://127.0.0.1:8000/api/users" `
  -H "Content-Type: application/json" `
  -H "Accept: application/json" `
  -d '{\"name\":\"Teszt Elek\",\"email\":\"teszt@example.hu\",\"password\":\"password123\",\"role\":\"user\"}'
```

**Posts lista:**

```powershell
curl -X GET "http://127.0.0.1:8000/api/posts" `
  -H "Accept: application/json"
```

**Új post létrehozása:**

```powershell
curl -X POST "http://127.0.0.1:8000/api/posts" `
  -H "Content-Type: application/json" `
  -H "Accept: application/json" `
  -d '{\"user_id\":2,\"title\":\"Teszt bejegyzés\",\"content\":\"Ez egy teszt tartalom.\",\"published_at\":\"2025-01-20 14:00:00\"}'
```

**Új comment létrehozása:**

```powershell
curl -X POST "http://127.0.0.1:8000/api/comments" `
  -H "Content-Type: application/json" `
  -H "Accept: application/json" `
  -d '{\"user_id\":3,\"post_id\":1,\"content\":\"Érdekes bejegyzés!\"}'
```

---

## 10. Postman használata

### 10.1. Postman Collection importálása

1. Nyisd meg a Postman alkalmazást
2. Kattints az **Import** gombra (bal felső sarokban)
3. Válaszd a **File** tab-ot
4. Tallózd ki: `c:\blog2\postman_collection.json`
5. Kattints az **Import** gombra

### 10.2. Környezeti változó beállítása

A collection-ben állítsd be a `baseUrl` változót:

**Collection szinten:**
1. Jobb klikk a Collection nevén → **Edit**
2. Válaszd a **Variables** tab-ot
3. Állítsd be:
   - Variable: `baseUrl`
   - Initial Value: `http://127.0.0.1:8000`
   - Current Value: `http://127.0.0.1:8000`

### 10.3. Végpontok tesztelése Postmanben

A collection mappaszerkezete:

```
Blog2 API
├── Users
│   ├── GET All Users
│   ├── GET User by ID
│   └── POST Create User
├── Posts
│   ├── GET All Posts
│   ├── GET Post by ID
│   └── POST Create Post
└── Comments
    ├── GET All Comments
    ├── GET Comment by ID
    └── POST Create Comment
```

**Tesztelési sorrend:**

1. **GET All Users** - Ellenőrizd, hogy vannak-e felhasználók
2. **GET User by ID** - Próbáld az ID=1-et
3. **POST Create User** - Hozz létre egy új felhasználót
4. **GET All Posts** - Nézd meg a létező bejegyzéseket
5. **POST Create Post** - Hozz létre új bejegyzést (használd az új user ID-t)
6. **POST Create Comment** - Kommentálj egy bejegyzéshez

### 10.4. Példa request body-k

**POST /api/users:**

```json
{
  "name": "Kovács János",
  "email": "janos.kovacs@example.hu",
  "password": "securepass123",
  "role": "user"
}
```

**POST /api/posts:**

```json
{
  "user_id": 2,
  "title": "Laravel tippek kezdőknek",
  "content": "Ebben a bejegyzésben Laravel fejlesztési tippeket osztok meg...",
  "published_at": "2025-01-20 14:00:00"
}
```

**POST /api/comments:**

```json
{
  "user_id": 3,
  "post_id": 1,
  "content": "Nagyon hasznos információk, köszönöm!"
}
```

---

## 11. Gyakori problémák

###  Probléma #1: "SQLSTATE[42S01]: Base table or view already exists"

**Ok:** Duplikált migráció vagy a tábla már létezik.

**Megoldás 1 - Ellenőrzés:**

```powershell
php artisan migrate:status
dir database\migrations
```

Keresd a duplikált fájlokat (pl. két `create_personal_access_tokens_table.php`).

**Megoldás 2 - Törlés és újra:**

```powershell
php artisan migrate:fresh --seed
```

**FIGYELEM:** Ez törli az összes adatot!

**Megoldás 3 - Kézi javítás:**

Töröld a duplikált migrációt:

```powershell
Remove-Item database\migrations\2025_XX_XX_XXXXXX_create_personal_access_tokens_table.php
```

---

###  Probléma #2: "Access denied for user 'root'@'localhost'"

**Ok:** Rossz MySQL jelszó vagy a szerver nem fut.

**Megoldás:**

1. Ellenőrizd, hogy a XAMPP MySQL fut-e (zöld a Control Panel-ben)
2. Ellenőrizd a `.env` fájlt:
   ```env
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Próbálj bejelentkezni manuálisan:
   ```powershell
   mysql -u root -p
   ```

---

###  Probléma #3: "Class 'App\Models\Post' not found"

**Ok:** Autoload cache probléma vagy hiányzó model fájl.

**Megoldás:**

```powershell
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
php artisan cache:clear
```

Ellenőrizd, hogy a model létezik: `app\Models\Post.php`

---

###  Probléma #4: "Vite manifest not found"

**Ok:** Nem futtattad az npm build-et.

**Megoldás:**

```powershell
npm install
npm run build
```

Vagy ha csak API-t használsz, távolítsd el a Blade template-kből a `@vite` direktívákat.

---

###  Probléma #5: "Permission denied" vagy "The stream or file could not be opened"

**Ok:** Windows file jogosultság probléma.

**Megoldás (PowerShell, admin joggal):**

```powershell
# Storage mappához írási jog
icacls storage /grant Everyone:F /T

# Bootstrap cache-hez írási jog
icacls bootstrap\cache /grant Everyone:F /T

# Vagy töröld a cache-t:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

###  Probléma #6: "Route [api.users.index] not defined"

**Ok:** A route-ok nincsenek regisztrálva vagy cache-elve van egy régi verzió.

**Megoldás:**

```powershell
php artisan route:clear
php artisan route:list
```

Ellenőrizd, hogy a `routes/api.php` tartalmazza a végpontokat.

---

###  Probléma #7: "CORS error" böngészőből

**Ok:** Cross-Origin Resource Sharing probléma (frontend-backend).

**Megoldás:**

Telepítsd a Laravel CORS middleware-t (ha nincs):

```powershell
composer require fruitcake/laravel-cors
```

Vagy használj Postman-t/cURL-t, ahol nincs CORS probléma.

---

## 12. Tesztelés checklist

###  Környezet ellenőrzés

```powershell
# PHP verzió
php -v  # >= 8.2

# Composer verzió
composer -V

# Node.js verzió
node -v  # >= 18

# MySQL fut?
mysql -u root -e "SHOW DATABASES;" | Select-String "blog2"
```

###  Laravel ellenőrzés

```powershell
# Migrációk státusza
php artisan migrate:status

# Route-ok listája
php artisan route:list --path=api

# Config cache törlése
php artisan config:clear
php artisan cache:clear
```

###  Adatok ellenőrzése (Tinker)

```powershell
php artisan tinker
```

```php
>>> User::count();
=> 3

>>> Post::count();
=> 5

>>> Comment::count();
=> 10

>>> User::first();
=> App\Models\User {#...}

>>> Post::with('user')->first();
=> App\Models\Post {#...}

>>> exit
```

###  API végpontok gyors teszt

| Végpont | Parancs | Várt státusz |
|---------|---------|--------------|
| GET /api/users | `curl http://127.0.0.1:8000/api/users` | 200 OK |
| GET /api/posts | `curl http://127.0.0.1:8000/api/posts` | 200 OK |
| GET /api/comments | `curl http://127.0.0.1:8000/api/comments` | 200 OK |
| GET /api/users/1 | `curl http://127.0.0.1:8000/api/users/1` | 200 OK |
| GET /api/posts/1 | `curl http://127.0.0.1:8000/api/posts/1` | 200 OK |

---

## 13. Feature tesztek

### 13.1. Tesztek futtatása

**Összes teszt:**

```powershell
php artisan test
```

**Csak Feature tesztek:**

```powershell
php artisan test --testsuite=Feature
```

**Verbose mód (részletes kimenet):**

```powershell
php artisan test --verbose
```

**Egy adott teszt osztály:**

```powershell
php artisan test --filter=UserApiTest
```

### 13.2. Teszt lefedettség

Ha telepítve van az Xdebug, generálhatsz coverage reportot:

```powershell
php artisan test --coverage
```

### 13.3. Teszt adatbázis

A tesztek automatikusan használnak egy in-memory SQLite adatbázist, így nem befolyásolják a fejlesztési adatbázist.

**phpunit.xml konfiguráció:**

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

---