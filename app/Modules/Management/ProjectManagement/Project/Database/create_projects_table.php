<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='/app/Modules/Management/ProjectManagement/Project/Database/create_projects_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_group_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('project_link');
            $table->string('project_agrement_file');
            $table->string('project_document');
            $table->integer('project_progress');
            $table->enum('project_status', ['Not Started', 'In Progress', 'Completed', 'On Hold'])->default('Not Started');
            $table->enum('delivery_status', ['Yet to meeting', 'Testing Phase', 'Successfully Delivered', 'Delivery process not started'])->default('Yet to meeting');
            $table->tinyInteger('client_rating')->nullable()->comment('Rating out of 10');

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
        Schema::dropIfExists('projects');
    }
};
