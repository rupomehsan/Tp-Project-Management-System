<?php
namespace App\Modules\Management\ProjectManagement\ProjectPlan\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\ProjectManagement\ProjectPlan\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\ProjectManagement\ProjectPlan\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        for ($i = 1; $i <= 100; $i++) {
            self::$model::create([                'project_id' => null,
                'module_id' => null,
                'title' => $faker->sentence,
                'start_date' => $faker->date,
                'end_date' => $faker->date,
                'is_done' => $faker->boolean,
            ]);
        }
    }
}