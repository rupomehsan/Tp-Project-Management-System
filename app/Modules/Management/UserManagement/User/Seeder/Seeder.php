<?php

namespace App\Modules\Management\UserManagement\User\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="\App\Modules\Management\UserManagement\User\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\UserManagement\User\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();
        
          self::$model::create([
            'name' => "super admin",
            'email' => "superadmin@gmail.com",
            'password' => Hash::make('@12345678'),
            'image' => 'avatar.png',
            'role_id' => 1,
        ]);

        self::$model::create([
            'name' => "admin",
            'email' => "admin@gmail.com",
            'password' => Hash::make('@12345678'),
            'image' => 'avatar.png',
            'role_id' => 2,
        ]);

        self::$model::create([
            'name' => "Md Ehsan",
            'email' => "ehsan@gmail.com",
            'password' => Hash::make('@12345678'),
            'image' => 'avatar.png',
            'role_id' => 3,
        ]);
        self::$model::create([
            'name' => "Md Mumin",
            'email' => "mumin@gmail.com",
            'password' => Hash::make('@12345678'),
            'image' => 'avatar.png',
            'role_id' => 4,
        ]);
        self::$model::create([
            'name' => "Md Nazmul",
            'email' => "nazmul@gmail.com",
            'password' => Hash::make('@12345678'),
            'image' => 'avatar.png',
            'role_id' => 5,
        ]);
        self::$model::create([
            'name' => "Md Abir",
            'email' => "abir@gmail.com",
            'password' => Hash::make('@12345678'),
            'image' => 'avatar.png',
            'role_id' => 6
        ]);
    }
}