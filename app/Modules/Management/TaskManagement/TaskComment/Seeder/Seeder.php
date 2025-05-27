<?php

namespace App\Modules\Management\TaskManagement\TaskComment\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="\App\Modules\Management\TaskManagement\TaskComment\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\TaskManagement\TaskComment\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();


        for ($i = 0; $i < 10; $i++) {
            self::$model::create([
                'task_id' => 1,
                'user_id' => 1,
                'comment' => $faker->sentence
            ]);
        }

        
    }
}