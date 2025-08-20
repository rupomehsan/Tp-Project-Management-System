<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='/app/Modules/Management/AttendanceManagement/Attendance/Database/create_attendances_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // employee reference
            $table->date('date'); // attendance date
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->enum('attendance_status', ['Present', 'Absent', 'Remote'])->default('Present');
            // Late info
            $table->boolean('is_late')->default(false);
            $table->unsignedInteger('late_minutes')->nullable(); // How many minutes late

            // Optional: work hours for future reporting
            $table->unsignedInteger('work_duration_minutes')->nullable();
            $table->unsignedInteger('overtime_minutes')->nullable(); // Overtime minutes if any

            // Optional: additional fields for attendance management
            $table->text('notes')->nullable(); // Any notes related to attendance


            $table->bigInteger('creator')->unsigned()->nullable();
            $table->string('slug', 50)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
