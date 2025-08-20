<?php

namespace App\Modules\Management\SystemLossManagement\SystemLoss\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\SystemLossManagement\SystemLoss\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\SystemLossManagement\SystemLoss\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'project_id' => 1,
            'loss_category_id' => 2,
            'loss_date' => '2025-06-24',
            'start_time' => '10:00:00',
            'end_time' => '11:30:00',
            'loss_type' => 'unplanned',
            'remarks' => 'Sudden power outage due to generator failure',
            'creator' => 1,
        ]);
        self::$model::create([
            'project_id' => 1,
            'loss_category_id' => 3,
            'loss_date' => '2025-06-23',
            'start_time' => '09:15:00',
            'end_time' => '10:00:00',
            'loss_type' => 'unplanned',
            'remarks' => 'Network went down due to ISP issue',
            'creator' => 1,
        ]);
        self::$model::create([
            'project_id' => 2,
            'loss_category_id' => 1,
            'loss_date' => '2025-06-22',
            'start_time' => '13:00:00',
            'end_time' => '14:00:00',
            'loss_type' => 'planned',
            'remarks' => 'Planned server maintenance',
            'creator' => 1,
        ]);
    }
}
