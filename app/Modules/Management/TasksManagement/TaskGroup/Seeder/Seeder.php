<?php

namespace App\Modules\Management\TasksManagement\TaskGroup\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\TasksManagement\TaskGroup\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\TasksManagement\TaskGroup\Models\Model::class;

    public function run(): void
    {
       $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'name' => "E-Commerce Group",
        ]);
        self::$model::create([
            'name' => "Inventory Group",
        ]);
        self::$model::create([
            'name' => "Shop Group",
        ]);
    }
}
