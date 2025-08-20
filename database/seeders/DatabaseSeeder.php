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
use App\Modules\Management\ProjectManagement\UpcommingProject\Seeder\Seeder as UpcommingProjectSeeder;
/**
 * Todo seeder management.
 */
use App\Modules\Management\TodoManagement\Category\Seeder\Seeder as CategorySeeder;
use App\Modules\Management\TodoManagement\AddTodo\Seeder\Seeder as AddTodoSeeder;
use App\Modules\Management\TodoManagement\Credential\Seeder\Seeder as CredentialSeeder;


/**
 * Task seeder management.
 */
use App\Modules\Management\TasksManagement\Tasks\Seeder\Seeder as TaskSeeder;
use App\Modules\Management\TasksManagement\TaskGroup\Seeder\Seeder as TaskGroupSeeder;


/**
 * Team seeder management.
 */
use App\Modules\Management\TeamManagement\Team\Seeder\Seeder as TeamSeeder;

/**
 * Attendance seeder management.
 */
use App\Modules\Management\AttendanceManagement\Attendance\Seeder\Seeder as AttendanceSeeder;

/**
 * Meeting seeder management.
 */
use App\Modules\Management\MeetingManagement\MeetingGroup\Seeder\Seeder as MeetingGroupSeeder;
use App\Modules\Management\MeetingManagement\Meeting\Seeder\Seeder as MeetingSeeder;
use App\Modules\Management\MeetingManagement\MeetingAgenda\Seeder\Seeder as MeetingAgendaSeeder;

/**
 * SystemLoss seeder management.
 */
use App\Modules\Management\SystemLossManagement\SystemLossCategory\Seeder\Seeder as SystemLossCategorySeeder;
use App\Modules\Management\SystemLossManagement\SystemLoss\Seeder\Seeder as SystemLossSeeder;




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
            UpcommingProjectSeeder::class,
            
            /**
             * Todo seeder management.
             */
            CategorySeeder::class,
            AddTodoSeeder::class,
            CredentialSeeder::class,
            /**
             * Task seeder management.
             */
            TaskSeeder::class,
            TaskGroupSeeder::class,
            /**
             * Team seeder management.
             */
            TeamSeeder::class,

            /**
             * Attendance seeder management.
             */
            AttendanceSeeder::class,
            
            /**
             * Meeting seeder management.
             */
            MeetingGroupSeeder::class,
            MeetingSeeder::class,
            MeetingAgendaSeeder::class,

            /**
             * SystemLoss seeder management.
             */
            SystemLossCategorySeeder::class,
            SystemLossSeeder::class,
        ]);
    }
}
