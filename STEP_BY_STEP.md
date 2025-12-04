# Blog2 — lépésről lépésre telepítési és tesztelési útmutató

Ez a dokumentáció segít gyorsan felállítani a projektet helyileg (Windows + XAMPP / php artisan serve), lefuttatni a migrációkat, feltölteni a mintadatokat és kipróbálni az API végpontokat Postman segítségével.

Elvárások (prerequisites)
- Windows gép
- XAMPP (Apache + MySQL) vagy működő MySQL + PHP telepítés
- PHP >= 8.2 és Composer telepítve (a projekt composer.json PHP 8.2-ot kér)
- (opcionális) Postman a végpontok teszteléséhez

Projekt helye a példában
- A projekt Laravel alkalmazása: `c:\xampp1\htdocs\blog2\blog`

1) Ellenőrizd a `.env`

- Nyisd meg `c:\xampp1\htdocs\blog2\blog\.env` és győződj meg róla, hogy a DB beállítások helyesek (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

Példa (alap XAMPP beállítás):

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog2
DB_USERNAME=root
DB_PASSWORD=

2) Telepítsd a függőségeket (Composer)

Nyisd meg PowerShell-t, és fusd:

cd 'c:\xampp1\htdocs\blog2\blog'
composer install

3) Készítsd el az adatbázist (ha még nincs)

Ha XAMPP phpMyAdmin-t használsz, nyisd meg http://localhost/phpmyadmin és hozz létre egy `blog2` adatbázist (utf8mb4) manuálisan.

Parancssorban (PowerShell):

# Ha nincs jelszó (root):
mysql -u root -e "CREATE DATABASE IF NOT EXISTS `blog2` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ha van root jelszó: mysql -u root -p -e "CREATE DATABASE ..."

4) Migrációk és seeder futtatása

- Először nézd meg a migrációk állapotát:

php artisan migrate:status

- Futtasd a migrációkat és a seeder-t:

php artisan migrate --seed --force

Megjegyzés: az első futtatásnál a rendszer kérhet megerősítést — `--force` parancs elnyomja az interaktív kérdést.

5) (Szükség esetén) ha a projekt használ frontendet / npm:

npm install
npm run build   # vagy npm run dev fejlesztéshez

6) Szerver indítása

- Fejlesztéshez egyszerűen futtasd:

php artisan serve --port=8000
# elérhető: http://127.0.0.1:8000/api

- Ha XAMPP+Apache használatával szeretnéd, állítsd be a VirtualHost-ot úgy, hogy a Laravel `public` könyvtára legyen a webroot (vagy helyezd a projektet megfelelően az `htdocs` alá). Ne feledd beállítani a `APP_URL` értékét `.env`-ben.

7) Postman használata (a gyűjtemény importálása)

- Importáld `c:\xampp1\htdocs\blog2\blog\postman_collection.json` fájlt a Postman-be.
- A collection tetején állítsd a `baseUrl` változót:
  - Ha `php artisan serve`-t használod: `http://127.0.0.1:8000`
  - Ha XAMPP és az app a `http://localhost/blog` alatt van: `http://localhost/blog`
- Lépések a teszteléshez a Postmanben:
  1. Auth -> Register (ha szeretnél új user-t létrehozni és token-t kapni)
  2. Auth -> Login (a válaszból a token elmentődik `authToken` változóba)
  3. Posts -> Create post (a kérelem Authorization fejléce `Bearer {{authToken}}` lesz)
  4. Comments -> Create comment (szintén használja a token-t)

8) Gyakori problémák és megoldások

- Hibajel: "SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'personal_access_tokens' already exists"

  Oka: van egy duplicate (ismétlődő) migráció, vagy korábban kézzel létrehoztad a táblát, és újra lefuttatva a migrációt a rendszer megpróbálja újra létrehozni.

  Megoldási lehetőségek:

  1) Ellenőrizd a migrációk állapotát és a duplikált migrációs fájlokat:

  php artisan migrate:status
  # Nézd meg a migrations könyvtárat: database/migrations
  dir database\migrations

  2) Ha találsz duplikált migrációs fájlt, töröld vagy nevezd át azt, amelyik későbbi időbélyeggel szerepel és ugyanazt a táblát hozza létre (például ha két külön fájl hozza létre `personal_access_tokens`-t).

  Remove-Item database\migrations\2025_12_04_095441_create_personal_access_tokens_table.php

  3) Alternatív megoldás: módosítsd a migráció `up()` metódusát, hogy guard-olva legyen (csak ha a tábla nem létezik):

  if (!Schema::hasTable('personal_access_tokens')) {
      Schema::create(...);
  }

  4) Ha a migráció már részben lefutott és rossz állapotban van, visszavonhatod a legutóbbi batch-t:

  php artisan migrate:rollback --step=1
  # vagy teljesen:
  php artisan migrate:reset
  php artisan migrate --seed

  5) Ha bizonytalan vagy, exportáld az adatbázist (biztonsági mentés), majd töröld kézzel a problémás táblát phpMyAdmin-ból, és futtasd újra a migrációt.

- Egyéb hibák
  - Permission hibák: ellenőrizd, hogy a PHP/Apache hozzáfér a projekt könyvtáraihoz.
  - Composer hibák: futtasd `composer install` újra, vagy `composer dump-autoload`.

9) Tesztelés — gyors ellenőrző lista

- Ellenőrizd, hogy a migrációk lefutottak:

php artisan migrate:status

- Ellenőrizd, hogy a seed feltöltötte a mintákat (users/posts/comments):

php artisan tinker
>>> \DB::table('users')->count();
>>> \DB::table('posts')->count();
>>> \DB::table('comments')->count();

- Teszteld az API végpontokat Postman-nel (példák lent a README-ben és a `API_DOCUMENTATION.md` fájlban).

10) Optional: Sanctum telepítés ellenőrzése

- A projekt composer.json-ban benne van a `laravel/sanctum`. Ha még nem publikáltad a szükséges asseteket/migrációkat (általában a csomag telepítése után automatikus), futtasd:

php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

11) Végső megjegyzések

- A projekt demo jellegű: nincsenek részletes jogosultságok vagy rate limiting beállítva.
- Éles környezetben használj HTTPS-t, erősebb jelszókövetelményeket, input validációt, rate limiting-et, és ügyelj a titkos környezeti változók (DB jelszó) biztonságára.

Ha szeretnéd, elvégzem helyetted a migrációk lefuttatását és a seed betöltését — indítsd el a MySQL szolgáltatást és mondd, hogy futtassam a `php artisan migrate --seed` parancsot. Ha tovább segítsek a personal_access_tokens hibával kapcsolatban, add meg, melyik fájlt szeretnéd megtartani/törölni és megnézem a jelenlegi migrációk állapotát.

---

Fájlok a projektben, amelyekre érdemes figyelni:
- `routes/api.php` — API útvonalak (users, posts, comments, auth)
- `app/Http/Controllers/Api/AuthController.php` — register/login/logout
- `app/Http/Controllers/Api/PostController.php`, `CommentController.php`, `UserController.php`
- `app/Models/Post.php`, `app/Models/Comment.php`, `app/Models/User.php` (User most már HasApiTokens)
- `database/migrations/` — itt találod a migrációs fájlokat
- `database/seeders/BlogSeeder.php` — a minták beszúrásához
- `postman_collection.json` — importáld Postman-be a gyors teszthez
