<?php

namespace Database\Seeders;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;

/**
 * User seeder management.
 */

use App\Modules\Management\UserManagement\Role\Seeder\Seeder as RoleSeeder;
use App\Modules\Management\UserManagement\User\Seeder\Seeder as UserSeeder;
use App\Modules\Management\SettingManagement\WebsiteSettings\Seeder\Seeder as WebsiteSettingsSeeder;

/**
 * task seeder management.
 */
use App\Modules\Management\TaskManagement\Task\Seeder\Seeder as TaskSeeder;
use App\Modules\Management\TaskManagement\TaskComment\Seeder\Seeder as TaskCommentSeeder;
use App\Modules\Management\TaskManagement\TaskFile\Seeder\Seeder as TaskFileSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            /**
             * User seeder management.
             */
            RoleSeeder::class,
            UserSeeder::class,
            WebsiteSettingsSeeder::class,
            
            /**
             * TaskCommentSeeder seeder management.
             */
            TaskSeeder::class,
            TaskCommentSeeder::class,
            TaskFileSeeder::class,


        ]);
    }
}