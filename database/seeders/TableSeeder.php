<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{

    /**
     * @var int
     */
    protected int $count = 20;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        Table::factory()->count($this->count)->create();
        Model::reguard();
    }
}

