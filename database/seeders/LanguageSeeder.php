<?php

namespace Database\Seeders;

use GuzzleHttp\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = config('laravellocalization.supportedLocales');
        collect($languages)->map(function ($language, $code) {
            DB::table('languages')->insert([
                'name' => $language['name'],
                'code' => $code,
                'native' => $language['native'],
                'regional' => $language['regional'],
            ]);
        });
    }
}
