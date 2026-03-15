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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Who did it
            $table->string('action'); // e.g., 'Update Stock', 'Delete Printer'
            $table->string('model_type'); // e.g., 'App\Models\Inventory'
            $table->unsignedBigInteger('model_id'); // ID of the item changed
            $table->json('before')->nullable(); // Previous data
            $table->json('after')->nullable(); // New data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
