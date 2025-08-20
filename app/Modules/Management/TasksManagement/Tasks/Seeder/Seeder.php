<?php

namespace App\Modules\Management\TasksManagement\Tasks\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\TasksManagement\Tasks\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\TasksManagement\Tasks\Models\Model::class;

    public function run(): void
    {
       $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'task_group_id' => 1,
            'project_id' => 1,
            'title' => 'Create UI Wireframes',
            'description' => 'Design the initial user interface wireframes for the client app.',
            'assigned_to' => 3,
            'start_date' => '2025-06-01',
            'end_date' => '2025-06-05',
            'task_status' => 'Pending',
            'priority' => 'normal',
            'creator' => 1,
        ]);
        self::$model::create([
            'task_group_id' => 1,
            'project_id' => 1,
            'title' => 'API Integration',
            'description' => 'Integrate authentication and product APIs.',
            'assigned_to' => 4,
            'start_date' => '2025-06-06',
            'end_date' => '2025-06-10',
            'task_status' => 'In Progress',
            'priority' => 'high',
            'creator' => 1,
        ]);
            self::$model::create([
            'task_group_id' => 2,
            'project_id' => 2,
            'title' => 'Final QA Testing',
            'description' => 'Conduct final round of quality assurance before launch.',
            'assigned_to' => 5,
            'start_date' => '2025-06-11',
            'end_date' => '2025-06-14',
            'task_status' => 'Pending',
            'priority' => 'urgent',
            'creator' => 1,
        ]);
    }
}
