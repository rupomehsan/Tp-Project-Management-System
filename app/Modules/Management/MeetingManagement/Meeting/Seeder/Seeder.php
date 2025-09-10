<?php

namespace App\Modules\Management\MeetingManagement\Meeting\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\MeetingManagement\Meeting\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\MeetingManagement\Meeting\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'group_id' => 1,
            'title' => 'Monthly Team Sync',
            'date' => now()->toDateString(),
            'meeting_type' => 'Offline',
            'meeting_link' => '',
            'description' => 'Discussion about monthly progress.',
            'creator' => 1,
        ]);
        self::$model::create([
            'group_id' => 2,
            'title' => 'Client Project Demo',
            'date' => now()->addDays(3)->toDateString(),
            'meeting_type' => 'Online',
            'meeting_link' => 'https://zoom.us/j/123456789',
            'description' => 'Demo of the new client features.',
            'creator' => 1,
        ]);
        self::$model::create([
            'group_id' => 3,
            'title' => 'Hybrid Strategy Planning',
            'date' => now()->addWeek()->toDateString(),
            'meeting_type' => 'Hybrid',
            'meeting_link' => 'https://meet.google.com/example-link',
            'description' => 'Planning for next quarter goals.',
            'creator' => 1,
        ]);
        self::$model::create([
            'group_id' => 4,
            'title' => 'Support Team Meeting',
            'date' => now()->addDays(10)->toDateString(),
            'meeting_type' => 'Offline',
            'meeting_link' => '',
            'description' => 'Reviewing support tickets and strategies.',
            'creator' => 1,
        ]);
        self::$model::create([
            'group_id' => 2,
            'title' => 'Weekly Standup',
            'date' => now()->addDays(1)->toDateString(),
            'meeting_type' => 'Online',
            'meeting_link' => 'https://teams.microsoft.com/l/meetup-join/123456789',
            'description' => 'Quick updates from each team member.',
            'creator' => 1,
        ]);
    }
}
