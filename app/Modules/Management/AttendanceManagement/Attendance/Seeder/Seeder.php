<?php

namespace App\Modules\Management\AttendanceManagement\Attendance\Seeder;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="App\Modules\Management\AttendanceManagement\Attendance\Seeder\Seeder"
     */
    static $model = \App\Modules\Management\AttendanceManagement\Attendance\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

        self::$model::create([
            'user_id' => 1,
            'date' => Carbon::today()->format('Y-m-d'),
            'check_in' => Carbon::today()->setTime(9, 0),
            'check_out' => Carbon::today()->setTime(18, 0),
            'attendance_status' => 'Present',
            'creator' => 1,
        ]);
        self::$model::create([
            'user_id' => 2,
            'date' => Carbon::today()->format('Y-m-d'),
            'check_in' => Carbon::today()->setTime(10, 30),
            'check_out' => Carbon::today()->setTime(18, 0),
            'attendance_status' => 'Absent',
            'creator' => 1,
        ]);
        self::$model::create([
            'user_id' => 3,
            'date' => Carbon::today()->format('Y-m-d'),
            'check_in' => null,
            'check_out' => null,
            'attendance_status' => 'Absent',
            'creator' => 1,
        ]);
    }
}
