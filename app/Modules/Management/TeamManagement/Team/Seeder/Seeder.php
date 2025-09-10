<?php

namespace App\Modules\Management\TeamManagement\Team\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\TeamManagement\Team\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\TeamManagement\Team\Models\Model::class;

    public function run(): void
    {
       $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'name' => 'ThreeTeam',
            'description' => 'Advanced inventory management system.',
            'creator' => 1,
        ]);
        self::$model::create([
            'name' => 'BatabiLebu',
            'description' => 'E-commerce solution for local shops.',
            'creator' => 1,
        ]);
            self::$model::create([
            'name' => 'SupportTeam',
            'description' => 'Multi-vendor eCommerce platform.',
            'creator' => 1,
        ]);
    }
}
