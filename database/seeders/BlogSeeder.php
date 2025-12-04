<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Users
        DB::table('users')->insert([
            [
                'name' => 'Kovács Ádám',
                'email' => 'adam.kovacs@example.hu',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Nagy Eszter',
                'email' => 'eszter.nagy@example.hu',
                'password' => Hash::make('password'),
                'role' => 'author',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Tóth Péter',
                'email' => 'peter.toth@example.hu',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Szabó Anna',
                'email' => 'anna.szabo@example.hu',
                'password' => Hash::make('password'),
                'role' => 'user',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Posts
        DB::table('posts')->insert([
            [
                'user_id' => 2,
                'title' => 'A magyar gasztronómia titkai',
                'content' => 'Magyar ételek, receptek és hagyományok részletes bemutatása.',
                'published_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'title' => 'Utazás Budapesten',
                'content' => 'Top 10 látnivaló Budapesten egy hosszú hétvégére.',
                'published_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Site bejelentés',
                'content' => 'Üdvözlünk a blogon! Itt fontos híreket fogunk megosztani.',
                'published_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Comments
        DB::table('comments')->insert([
            [
                'user_id' => 3,
                'post_id' => 1,
                'content' => 'Szuper cikk, köszönöm a recepteket!',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 4,
                'post_id' => 1,
                'content' => 'Mellékelhetnél több fotót a fogásokról.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 3,
                'post_id' => 2,
                'content' => 'A Duna-korzó valóban gyönyörű tavasszal.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'post_id' => 3,
                'content' => 'Köszönöm az üdvözlést! Várom a további posztokat.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
