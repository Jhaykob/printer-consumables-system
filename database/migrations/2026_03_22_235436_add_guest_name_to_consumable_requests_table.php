<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumable_requests', function (Blueprint $table) {
            // Add a nullable string to store the manual name
            $table->string('guest_name')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('consumable_requests', function (Blueprint $table) {
            $table->dropColumn('guest_name');
        });
    }
};
