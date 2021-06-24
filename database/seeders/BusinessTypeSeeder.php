<?php


namespace Database\Seeders;


use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(config('business.types'))->each(function ($type) {
            $businessType = BusinessType::firstOrNew(['type' => $type]);
            if (!$businessType->exists) $businessType->save();
        });
    }
}
