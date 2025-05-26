<?php

namespace App\Modules\Management\ProjectManagement\ProjectGroup\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\ProjectManagement\ProjectGroup\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\ProjectManagement\ProjectGroup\Models\Model::class;

    public function run(): void
    {
       $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'name' => "E-Commerce ",
        ]);
        self::$model::create([
            'name' => "Inventory",
        ]);
            self::$model::create([
            'name' => "Shop",
        ]);
    }
}
