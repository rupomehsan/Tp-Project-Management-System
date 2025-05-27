<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='\App\Modules\Management\TaskManagement\TaskFile\Database\create_tasks_files_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks_files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('task_id')->nullable();
            $table->string('file_name', 100)->nullable();
            $table->string('file_path')->nullable();
            $table->timestamp('uploaded_at')->nullable();

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
        Schema::dropIfExists('tasks_files');
    }
};