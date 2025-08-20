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
            'name' => 'E-commerce Platform',
            'description' => 'An online shopping platform with cart and payment integration.',
            'start_date' => '2024-01-15',
            'end_date' => '2024-04-30',
            'project_link' => 'https://ecommerce.example.com',
            'project_agrement_file' => 'files/ecommerce_agreement.pdf',
            'project_document' => 'files/ecommerce_docs.zip',
            'project_progress' => 100,
            'project_status' => 'Completed',
            'delivery_status' => 'Successfully Delivered',
            'client_rating' => 9,
            'creator' => 1,
        ]);
        self::$model::create([
            'project_group_id' => 2,
            'name' => 'Internal HR System',
            'description' => 'HR management for employee tracking and payroll.',
            'start_date' => '2024-03-01',
            'end_date' => '2024-06-15',
            'project_link' => 'https://hr.example.com',
            'project_agrement_file' => 'files/hr_agreement.pdf',
            'project_document' => 'files/hr_docs.zip',
            'project_progress' => 80,
            'project_status' => 'In Progress',
            'delivery_status' => 'Testing Phase',
            'client_rating' => 8,
            'creator' => 1,
        ]);
        self::$model::create([
            'project_group_id' => 3,
            'name' => 'Portfolio Website',
            'description' => 'Personal portfolio with blog and contact features.',
            'start_date' => '2024-05-10',
            'end_date' => null,
            'project_link' => 'https://portfolio.example.com',
            'project_agrement_file' => 'files/portfolio_agreement.pdf',
            'project_document' => 'files/portfolio_docs.zip',
            'project_progress' => 20,
            'project_status' => 'Not Started',
            'delivery_status' => 'Delivery process not started',
            'client_rating' => null,
            'creator' => 1,
        ]);
    }
}
