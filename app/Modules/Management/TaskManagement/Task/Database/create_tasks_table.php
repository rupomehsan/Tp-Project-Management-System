<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='\App\Modules\Management\TaskManagement\Task\Database\create_tasks_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->nullable();
            $table->string('title', 150)->nullable();
            $table->text('description')->nullable();
            $table->integer('system_loss')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('priority',['low', 'normal', 'high', 'urgent'])->default('low');
            $table->enum('task_status', ['Pending', 'In Progress', 'Completed'])->default('Pending');

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