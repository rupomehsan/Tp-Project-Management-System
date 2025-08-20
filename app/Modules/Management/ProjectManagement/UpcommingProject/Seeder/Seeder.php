<?php

namespace App\Modules\Management\ProjectManagement\UpcommingProject\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\ProjectManagement\UpcommingProject\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\ProjectManagement\UpcommingProject\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'project_group_id'     => 2,
            'name'                 => 'Internal CRM System',
            'description'          => 'CRM for sales pipeline, lead tracking and reporting.',
            'start_date'           => '2023-09-05',
            'end_date'             => '2024-06-30',
            'project_link'         => 'https://crm.example.com',
            'project_agrement_file' => 'files/crm_agreement.pdf',
            'project_document'     => 'files/crm_docs.zip',
            'project_progress'     => 100,
            'project_status'       => 'Not Started',
            'delivery_status'      => 'Yet to meeting',
            'client_rating'        => 5,
            'creator'              => 1,
        ]);
        self::$model::create([
            'project_group_id'     => 1,
            'name'                 => 'Mobile Banking App',
            'description'          => 'Cross-platform Flutter app for mobile banking and bill payments.',
            'start_date'           => '2024-01-20',
            'end_date'             => null,
            'project_link'         => 'https://playstore.example.com/mobilebank',
            'project_agrement_file' => 'files/mobilebank_agreement.pdf',
            'project_document'     => 'files/mobilebank_docs.zip',
            'project_progress'     => 80,
            'project_status'       => 'Not Started',
            'delivery_status'      => 'Yet to meeting',
            'client_rating'        => null,
            'creator'              => 2,
        ]);
        self::$model::create([
            'project_group_id'     => 2,
            'name'                 => 'E-Commerce Platform',
            'description'          => 'Full-stack e-commerce site with inventory, cart and payment gateway.',
            'start_date'           => '2024-03-15',
            'end_date'             => '2024-11-30',
            'project_link'         => 'https://shop.example.com',
            'project_agrement_file' => 'files/ecommerce_agreement.pdf',
            'project_document'     => 'files/ecommerce_docs.zip',
            'project_progress'     => 55,
            'project_status'       => 'Not Started',
            'delivery_status'      => 'Yet to meeting',
            'client_rating'        => null,
            'creator'              => 1,
        ]);
    }
}
