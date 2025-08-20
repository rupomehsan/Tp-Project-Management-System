<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='/app/Modules/Management/TasksManagement/Tasks/Database/create_tasks_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_group_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->enum('task_status', ['Pending', 'In Progress', 'Completed', 'Not Completed'])->default('Pending');
            $table->enum('task_user_status', ['Pending', 'In Progress', 'Completed', 'Not Completed'])->default('Pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('low');
            $table->tinyInteger('rating')->nullable()->comment('Rating out of 5')->default(0);

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
        Schema::dropIfExists('tasks');
    }
};
