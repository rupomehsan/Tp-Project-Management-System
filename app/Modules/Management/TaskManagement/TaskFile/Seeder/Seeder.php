<?php

namespace App\Modules\Management\TaskManagement\TaskFile\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Carbon\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="\App\Modules\Management\TaskManagement\TaskFile\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\TaskManagement\TaskFile\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();


        for ($i = 0; $i < 10; $i++) {
        self::$model::create([
            'task_id' => 1,
            'file_name' => $faker->name(),
            'file_path' => $faker->filePath(),
            'uploaded_at' => Carbon::now()->addDays($i),
        ]);
    }

        
    }
}