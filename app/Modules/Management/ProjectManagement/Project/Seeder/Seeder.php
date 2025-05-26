<?php

namespace App\Modules\Management\ProjectManagement\Project\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\ProjectManagement\Project\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\ProjectManagement\Project\Models\Model::class;

    public function run(): void
    {
       $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
           'project_group_id' => 1,
            'name' => 'InventoryPro',
            'description' => 'Advanced inventory management system.',
            'start_date' => '2025-06-01',
            'end_date' => '2025-09-01',
            'project_link' => 'none',
            'project_agrement_file' => 'none',
            'project_document' => 'none',
            'project_progress' => 4,
            'project_status' => 'In Progress',
            'creator' => 1,
        ]);
        self::$model::create([
            'project_group_id' => 2,
            'name' => 'ShopMaster',
            'description' => 'E-commerce solution for local shops.',
            'start_date' => '2025-04-15',
            'end_date' => '2025-08-15',
           'project_link' => 'none',
            'project_agrement_file' => 'none',
            'project_document' => 'none',
            'project_progress' => 6,
            'project_status' => 'Not Started',
            'creator' => 1,
        ]);
            self::$model::create([
            'project_group_id' => 3,
            'name' => 'RetailHub',
            'description' => 'Multi-vendor eCommerce platform.',
            'start_date' => '2025-01-01',
            'end_date' => '2025-05-01',
           'project_link' => 'none',
            'project_agrement_file' => 'none',
            'project_document' => 'none',
            'project_progress' => 5,
            'project_status' => 'Completed',
            'creator' => 1,
        ]);
    }
}
