<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='/app/Modules/Management/ProjectManagement/ProjectPlan/Database/create_project_plans_management_table.php'
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('project_plans_management', function (Blueprint $table) {

            $table->id();

            $table->bigInteger('project_plan_id')->nullable();
            $table->bigInteger('project_id')->nullable();
            $table->bigInteger('module_id')->nullable();
            $table->string('management_name', 100)->nullable();
            $table->date('date')->nullable();
            $table->tinyInteger('is_done')->default(0);

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
        Schema::dropIfExists('project_plans_management');
    }
};
