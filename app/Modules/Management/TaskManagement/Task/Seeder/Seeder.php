<?php

namespace App\Modules\Management\TaskManagement\Task\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="\App\Modules\Management\TaskManagement\Task\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\TaskManagement\Task\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();


        for ($i = 0; $i < 10; $i++) {
            self::$model::create([
                'project_id' => 1,
                'title' => $faker->sentence, // or $faker->jobTitle
                'description' => $faker->paragraph,
                'system_loss' => 1,
                'assigned_to' => 1,
                'priority' => 'low',
                'task_status' => 'Pending',
                'start_date' => Carbon::now()->addDays($i),
                'due_date' => Carbon::now()->addDays($i + 7),
            ]);
        }

        
    }
}