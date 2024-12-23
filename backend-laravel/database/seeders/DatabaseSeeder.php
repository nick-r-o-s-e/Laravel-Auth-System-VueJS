<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'country' => 'United Kingdom',
            'language' => "English"

        ]);

        $data = json_decode(Storage::get('country-by-languages.json'), true);


        if (!$data) {
            dd("Error loading JSON Data:", Storage::get('country-by-languages.json'));
            dd(Storage::exists('country-by-languages.json'));
        }

        foreach ($data as $item) {
            DB::table('countries')->updateOrInsert(['name' => $item['country']], ['name' => $item['country']]);

            $countryId = DB::table('countries')->where('name', $item['country'])->value('id');

            foreach ($item['languages'] as $languageName) {
                DB::table('languages')->updateOrInsert(['name' => $languageName], ['name' => $languageName]);

                $languageId = DB::table('languages')->where('name', $languageName)->value('id');

                DB::table('country_language')->updateOrInsert(
                    ['country_id' => $countryId, 'language_id' => $languageId],
                    ['country_id' => $countryId, 'language_id' => $languageId]
                );
            }
        }
    }
}
