<?php

namespace App\Modules\Management\SystemLossManagement\SystemLossCategory\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\SystemLossManagement\SystemLossCategory\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\SystemLossManagement\SystemLossCategory\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'title' => 'Data Loss',
            'creator' => 1,
        ]);
        self::$model::create([
            'title' => 'Backup Failure',
        ]);
        self::$model::create([
            'title' => 'Electrical Failure',
            'creator' => 1,
        ]);
        self::$model::create([
            'title' => 'Network Failure',
            'creator' => 1,
        ]);
        self::$model::create([
            'title' => 'Hardware Failure',
            'creator' => 1,
        ]);
        self::$model::create([
            'title' => 'Meeting  Loss',
            'creator' => 1,
        ]);
    }
}
