<?php

namespace App\Modules\Management\TodoManagement\AddTodo\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\TodoManagement\AddTodo\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\TodoManagement\AddTodo\Models\Model::class;

    public function run(): void
    {
       $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'category_id' => 1,
            'description' => 'Design the initial user interface wireframes for the client app.',
            'creator' => 1,
        ]);
        self::$model::create([
            'category_id' => 3,
            'description' => 'Integrate authentication and product APIs.',
            'creator' => 1,
        ]);
            self::$model::create([
            'category_id' => 2,
            'description' => 'Conduct final round of quality assurance before launch.',
        ]);
    }
}
