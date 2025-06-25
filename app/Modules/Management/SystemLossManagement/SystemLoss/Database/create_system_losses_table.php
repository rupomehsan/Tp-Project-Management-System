<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='/app/Modules/Management/SystemLossManagement/SystemLoss/Database/create_system_losses_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_losses', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('loss_category_id')->nullable();
            $table->date('loss_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('loss_type', ['planned', 'unplanned'])->default('unplanned');
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('system_losses');
    }
};
