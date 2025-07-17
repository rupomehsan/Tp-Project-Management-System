<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate --path='/app/Modules/Management/Message/Database/add_group_chat_fields_to_conversation_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversation', function (Blueprint $table) {
            $table->boolean('is_group')->default(false)->after('participant');
            $table->string('group_name')->nullable()->after('is_group');
            $table->json('group_participants')->nullable()->after('group_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversation', function (Blueprint $table) {
            $table->dropColumn(['is_group', 'group_name', 'group_participants']);
        });
    }
};
