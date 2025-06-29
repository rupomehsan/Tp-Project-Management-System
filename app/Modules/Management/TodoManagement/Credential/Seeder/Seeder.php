<?php

namespace App\Modules\Management\TodoManagement\Credential\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\TodoManagement\Credential\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\TodoManagement\Credential\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'title'       => 'Demo Account',
            'email'       => 'demo@example.com',
            'password'    => '@12345678',
            'description' => 'Seeded credential for testing.',
            'creator' => 1,
        ]);
        self::$model::create([
            'title'       => 'Demo Account',
            'email'       => 'demo@example.com',
            'password'    => '@12345678',
            'description' => 'Seeded credential for testing.',
            'creator' => 1,
        ]);
        self::$model::create([
            'title'       => 'GitHub',
            'email'       => 'rahim.dev@example.com',
            'password'    => 'GhP@ssw0rd!',
            'description' => 'Personal access token read-only repos.',
            'creator'     => 1,
        ]);
    }
}
