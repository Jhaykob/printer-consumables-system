<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->string('status')->default('Pending'); // Pending, Approved, Denied, Fulfilled, Recalled
            $table->text('rejection_reason')->nullable();
            $table->text('recall_reason')->nullable();
            $table->string('recall_action')->nullable(); // e.g., 'restock', 'dispose'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            //
        });
    }
};
