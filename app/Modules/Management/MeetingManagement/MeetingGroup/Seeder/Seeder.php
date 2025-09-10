<?php

namespace App\Modules\Management\MeetingManagement\MeetingGroup\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\MeetingManagement\MeetingGroup\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\MeetingManagement\MeetingGroup\Models\Model::class;

    public function run(): void
    {
       $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'title' => 'Internal Meeting',
            'creator' => 1,
        ]);
        self::$model::create([
            'title' => 'Client Meeting',
            'creator' => 1,
        ]);
        self::$model::create([
            'title' => 'Planning Meeting',
            'creator' => 1,
        ]);
        self::$model::create([
            'title' => 'Support Meeting',
            'creator' => 1,
        ]);
    }
}
