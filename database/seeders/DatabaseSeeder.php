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
 * project seeder management.
 */

use App\Modules\Management\ProjectManagement\ProjectGroup\Seeder\Seeder as ProjectGroupSeeder;
use App\Modules\Management\ProjectManagement\Project\Seeder\Seeder as ProjectSeeder;
/**
 * Todo seeder management.
 */
use App\Modules\Management\TodoManagement\Category\Seeder\Seeder as CategorySeeder;
use App\Modules\Management\TodoManagement\AddTodo\Seeder\Seeder as AddTodoSeeder;

/**
 * Task seeder management.
 */

use App\Modules\Management\TasksManagement\Tasks\Seeder\Seeder as TaskSeeder;


/**
 * Team seeder management.
 */

use App\Modules\Management\TeamManagement\Team\Seeder\Seeder as TeamSeeder;


// use App\Modules\Management\BlogCategory\Seeder\Seeder as BlogCategorySeeder;

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
             * Project seeder management.
             */
            ProjectGroupSeeder::class,
            ProjectSeeder::class,
            /**
             * Todo seeder management.
             */
            CategorySeeder::class,
            AddTodoSeeder::class,
            /**
             * Task seeder management.
             */
            TaskSeeder::class,
            /**
             * Team seeder management.
             */
            TeamSeeder::class,

        ]);
    }
}
