<?php

namespace App\Modules\Management\MeetingManagement\MeetingAgenda\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\MeetingManagement\MeetingAgenda\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\MeetingManagement\MeetingAgenda\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'meeting_id' => 1,
            'title' => 'Project Kickoff Discussion',
            'description' => 'Overview of project goals, timelines, and key deliverables.',

            'creator' => 1,
        ]);
        self::$model::create([
            'meeting_id' => 1,
            'title' => 'Technical Stack Review',
            'description' => 'Evaluate tools, frameworks, and infrastructure to be used.',
            'creator' => 1,
        ]);
        self::$model::create([
            'meeting_id' => 2,
            'title' => 'Client Feedback Implementation',
            'description' => 'Discuss how to incorporate feedback into next sprint.',
            'creator' => 1,
        ]);
    }
}
